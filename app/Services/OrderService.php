<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * @param  array{customer_id?: ?int, table_id?: ?int, note?: ?string, items: array<int, array{product_id: int, quantity: int}>}  $data
     */
    public function create(array $data, User $user): Sale
    {
        return DB::transaction(function () use ($data, $user) {
            $sale = Sale::create([
                'customer_id' => $data['customer_id'] ?? null,
                'table_id' => $data['table_id'] ?? null,
                'user_id' => $user->id,
                'note' => $data['note'] ?? null,
                'sub_total' => 0,
                'descount' => 0,
                'total' => 0,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            $this->syncItems($sale, $this->quantities($data['items']), $user);

            $sale->table?->syncStatus();

            return $sale;
        });
    }

    /**
     * Replace the order's items, moving only the stock difference.
     */
    public function update(Sale $sale, array $data, User $user): Sale
    {
        return DB::transaction(function () use ($sale, $data, $user) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            if (! $sale->isEditable()) {
                throw ValidationException::withMessages([
                    'items' => 'Only unpaid orders that the kitchen has not started can be edited.',
                ]);
            }

            $oldTable = $sale->table;

            $sale->update([
                'customer_id' => $data['customer_id'] ?? null,
                'table_id' => $data['table_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            $this->syncItems($sale, $this->quantities($data['items']), $user);

            $oldTable?->syncStatus();
            $sale->refresh()->table?->syncStatus();

            return $sale;
        });
    }

    /**
     * @param  array{payment_method: string, descount?: ?float, amount_received?: ?float}  $data
     */
    public function checkout(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            if (! $sale->canCheckout()) {
                throw ValidationException::withMessages([
                    'payment' => 'This order is already paid or was cancelled.',
                ]);
            }

            $discount = min((float) ($data['descount'] ?? 0), (float) $sale->sub_total);
            $total = round((float) $sale->sub_total - $discount, 2);

            if ($data['payment_method'] === 'cash') {
                $received = (float) ($data['amount_received'] ?? 0);

                if ($received < $total) {
                    throw ValidationException::withMessages([
                        'amount_received' => 'Amount received is less than the total ('.number_format($total, 2).').',
                    ]);
                }
            } else {
                $received = $total;
            }

            $sale->update([
                'descount' => $discount,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'amount_received' => $received,
                'change_due' => round($received - $total, 2),
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            $sale->table?->syncStatus();

            return $sale;
        });
    }

    public function changeStatus(Sale $sale, string $status, User $user, ?string $reason = null): Sale
    {
        return DB::transaction(function () use ($sale, $status, $user, $reason) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            if (! in_array($status, $sale->nextStatusesFor($user), true)) {
                throw ValidationException::withMessages([
                    'status' => "You can't move this order from {$sale->status} to {$status}.",
                ]);
            }

            if ($status === 'cancelled') {
                $this->cancel($sale, $user, $reason);
            }

            $sale->update([
                'status' => $status,
                'sent_at' => $status === 'sent' ? now() : $sale->sent_at,
            ]);

            $sale->table?->syncStatus();

            return $sale;
        });
    }

    /**
     * Record who cancelled and why. Items the kitchen hasn't started go back
     * to stock; once cooking has begun the food is written off instead.
     */
    private function cancel(Sale $sale, User $user, ?string $reason): void
    {
        $reason = trim((string) $reason) ?: null;

        if (! $reason && $sale->cancelNeedsReason()) {
            throw ValidationException::withMessages([
                'cancel_reason' => 'Give a reason for cancelling an order the kitchen already has.',
            ]);
        }

        if ($sale->restocksOnCancel()) {
            $this->restock($sale, $user);
        }

        $sale->fill([
            'cancelled_at' => now(),
            'cancelled_by' => $user->id,
            'cancelled_from' => $sale->status,
            'cancel_reason' => $reason,
        ]);
    }

    /**
     * Return a cancelled order's items to stock. The items stay on the
     * order so it still shows what was ordered.
     */
    private function restock(Sale $sale, User $user): void
    {
        foreach ($sale->items as $item) {
            Product::whereKey($item->product_id)->increment('stock', $item->quantity);

            StockMovement::create([
                'product_id' => $item->product_id,
                'user_id' => $user->id,
                'sale_id' => $sale->id,
                'type' => 'in',
                'quantity' => $item->quantity,
                'note' => "Order {$sale->order_number} cancelled",
            ]);
        }
    }

    /**
     * Make the order's items match $wanted (product id => quantity),
     * adjusting stock and recording a movement for each change.
     *
     * @param  Collection<int, int>  $wanted
     */
    private function syncItems(Sale $sale, Collection $wanted, User $user): void
    {
        $existing = $sale->items()->get()->keyBy('product_id');

        $products = Product::whereIn('id', $wanted->keys()->merge($existing->keys())->unique())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $note = "Order {$sale->order_number}";
        $subtotal = 0;

        foreach ($wanted->keys()->merge($existing->keys())->unique() as $productId) {
            $product = $products->get($productId);
            $item = $existing->get($productId);
            $newQty = $wanted->get($productId, 0);
            $delta = $newQty - ($item?->quantity ?? 0);

            if ($delta > 0 && ! $item && ! $product?->status) {
                throw ValidationException::withMessages([
                    'items' => ($product?->name ?? 'A product').' is not available.',
                ]);
            }

            if ($delta > 0 && $product->stock < $delta) {
                throw ValidationException::withMessages([
                    'items' => "Not enough stock for {$product->name}. Only {$product->stock} left.",
                ]);
            }

            if ($delta !== 0 && $product) {
                $product->decrement('stock', $delta);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'sale_id' => $sale->id,
                    'type' => $delta > 0 ? 'out' : 'in',
                    'quantity' => abs($delta),
                    'note' => $note,
                ]);
            }

            if ($newQty === 0) {
                $item?->delete();

                continue;
            }

            // Items already on the order keep the price they were ordered at.
            $unitPrice = $item ? (float) $item->unit_price : (float) $product->price;
            $lineTotal = round($unitPrice * $newQty, 2);
            $subtotal += $lineTotal;

            $sale->items()->updateOrCreate(
                ['product_id' => $productId],
                [
                    'product_name' => $item->product_name ?? $product->name,
                    'quantity' => $newQty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $item->unit_cost ?? $product->cost,
                    'subtotal' => $lineTotal,
                ],
            );
        }

        $sale->update([
            'sub_total' => $subtotal,
            'total' => max($subtotal - (float) $sale->descount, 0),
        ]);
    }

    /**
     * Combine submitted items into product id => total quantity.
     *
     * @return Collection<int, int>
     */
    private function quantities(array $items): Collection
    {
        return collect($items)
            ->groupBy(fn ($item) => (int) $item['product_id'])
            ->map(fn ($rows) => (int) $rows->sum('quantity'));
    }
}
