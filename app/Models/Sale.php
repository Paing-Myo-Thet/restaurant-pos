<?php

namespace App\Models;

use App\Enums\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public const PAYMENT_METHODS = ['cash', 'card', 'mobile'];

    /**
     * Statuses where the kitchen hasn't started cooking, so cancelling returns
     * the items to stock. Cancelled later, the food is written off as waste.
     */
    public const RESTOCKABLE_STATUSES = ['pending', 'sent'];

    /**
     * Common reasons offered when cancelling; staff can also type their own.
     */
    public const CANCEL_REASONS = [
        'Customer changed their mind',
        'Customer left',
        'Ordered by mistake',
        'Item not available',
        'Kitchen took too long',
        'Food quality complaint',
    ];

    /**
     * Allowed status changes and the permission each one needs.
     */
    public const TRANSITIONS = [
        'pending' => [
            'sent' => Permission::OrdersManage,
            'cancelled' => Permission::OrdersManage,
        ],
        'sent' => [
            'preparing' => Permission::KitchenManage,
            'cancelled' => Permission::OrdersCancel,
        ],
        'preparing' => [
            'ready' => Permission::KitchenManage,
            'cancelled' => Permission::OrdersVoid,
        ],
        'ready' => [
            'served' => Permission::OrdersManage,
            'cancelled' => Permission::OrdersVoid,
        ],
    ];

    protected $fillable = [
        'customer_id',
        'table_id',
        'user_id',
        'sub_total',
        'descount',
        'total',
        'status',
        'payment_status',
        'note',
        'payment_method',
        'amount_received',
        'change_due',
        'sent_at',
        'paid_at',
        'cancelled_at',
        'cancelled_by',
        'cancelled_from',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'sub_total' => 'decimal:2',
            'descount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_received' => 'decimal:2',
            'change_due' => 'decimal:2',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function getOrderNumberAttribute(): string
    {
        return '#'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Orders still in progress: not cancelled, and not both served and paid.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', 'cancelled')
            ->where(fn (Builder $q) => $q
                ->where('status', '!=', 'served')
                ->orWhere('payment_status', 'unpaid'));
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isEditable(): bool
    {
        return ! $this->isPaid() && in_array($this->status, ['pending', 'sent'], true);
    }

    public function canCheckout(): bool
    {
        return ! $this->isPaid() && $this->status !== 'cancelled';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Whether cancelling now would return the items to stock.
     */
    public function restocksOnCancel(): bool
    {
        return in_array($this->status, self::RESTOCKABLE_STATUSES, true);
    }

    /**
     * Cancelled after the kitchen had started, so the food was wasted.
     */
    public function wasWrittenOff(): bool
    {
        return $this->isCancelled() && $this->cancelled_from !== null
            && ! in_array($this->cancelled_from, self::RESTOCKABLE_STATUSES, true);
    }

    /**
     * Once the kitchen has the order, cancelling needs a reason for the record.
     */
    public function cancelNeedsReason(): bool
    {
        return $this->status !== 'pending';
    }

    /**
     * Cancelled orders never print. Whoever takes payment can print or reprint at any time; staff who can
     * only print drafts get the bill once the food is served.
     */
    public function canBePrintedBy(User $user): bool
    {
        // A bill for a cancelled order could be used to collect cash off the books.
        if ($this->isCancelled()) {
            return false;
        }

        if ($user->hasPermission(Permission::OrdersCheckout)) {
            return true;
        }

        return $user->hasPermission(Permission::OrdersPrintDraft) && $this->status === 'served';
    }

    /**
     * Status changes the given user may make from the current status.
     *
     * @return list<string>
     */
    public function nextStatusesFor(User $user): array
    {
        $next = [];

        foreach (self::TRANSITIONS[$this->status] ?? [] as $status => $permission) {
            if ($status === 'cancelled' && $this->isPaid()) {
                continue;
            }

            if ($user->hasPermission($permission)) {
                $next[] = $status;
            }
        }

        return $next;
    }
}
