<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $sale->isPaid() ? 'Receipt' : 'Bill' }} {{ $sale->order_number }}</title>

    <style>
        /* Sized for 80mm thermal receipt printers. */
        @page { size: 80mm auto; margin: 4mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f4f6; font: 12px/1.45 ui-monospace, Menlo, Consolas, monospace; color: #000; }
        .receipt { width: 72mm; margin: 16px auto; padding: 12px; background: #fff; }
        .center { text-align: center; }
        h1 { margin: 0; font-size: 16px; }
        .muted { color: #444; }
        hr { border: 0; border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; }
        .right { text-align: right; }
        .total td { font-size: 15px; font-weight: bold; padding-top: 4px; }
        .actions { width: 72mm; margin: 0 auto 16px; display: flex; gap: 8px; }
        .actions button { flex: 1; padding: 8px; font: inherit; cursor: pointer; }
        @media print {
            body { background: #fff; }
            .receipt { margin: 0; padding: 0; width: auto; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

    <div class="receipt">
        <div class="center">
            <h1>{{ config('app.name', 'Restaurant POS') }}</h1>
            <div class="muted">{{ $sale->isPaid() ? 'RECEIPT' : 'BILL — NOT PAID' }}</div>
        </div>

        <hr>

        <table>
            <tr><td>Order</td><td class="right">{{ $sale->order_number }}</td></tr>
            <tr><td>Date</td><td class="right">{{ ($sale->paid_at ?? $sale->created_at)->format('d/m/Y h:i A') }}</td></tr>
            <tr><td>Table</td><td class="right">{{ $sale->table?->name ?? 'Takeaway' }}</td></tr>
            @if ($sale->customer)
                <tr><td>Customer</td><td class="right">{{ $sale->customer->name }}</td></tr>
            @endif
            <tr><td>Served by</td><td class="right">{{ $sale->user?->name }}</td></tr>
        </table>

        <hr>

        <table>
            @foreach ($sale->items as $item)
                <tr>
                    <td colspan="2">{{ $item->product_name }}</td>
                </tr>
                <tr>
                    <td class="muted">&nbsp;&nbsp;{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </table>

        <hr>

        <table>
            <tr><td>Subtotal</td><td class="right">{{ number_format($sale->sub_total, 2) }}</td></tr>
            @if ($sale->descount > 0)
                <tr><td>Discount</td><td class="right">-{{ number_format($sale->descount, 2) }}</td></tr>
            @endif
            <tr class="total"><td>TOTAL (Ks)</td><td class="right">{{ number_format($sale->total, 2) }}</td></tr>

            @if ($sale->isPaid())
                <tr><td>Paid by</td><td class="right">{{ ucfirst($sale->payment_method ?? '-') }}</td></tr>
                @if ($sale->payment_method === 'cash')
                    <tr><td>Cash</td><td class="right">{{ number_format($sale->amount_received, 2) }}</td></tr>
                    <tr><td>Change</td><td class="right">{{ number_format($sale->change_due, 2) }}</td></tr>
                @endif
            @endif
        </table>

        <hr>

        <div class="center muted">Thank you! Please come again.</div>
    </div>

    <div class="actions">
        <button type="button" onclick="window.print()">Print</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
