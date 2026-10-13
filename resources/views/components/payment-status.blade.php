@props(['sale'])

@if ($sale->payment_status === 'paid')
    <span {{ $attributes->class('inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800') }}>
        Paid{{ $sale->payment_method ? ' · '.ucfirst($sale->payment_method) : '' }}
    </span>
@else
    <span {{ $attributes->class('inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800') }}>
        Unpaid
    </span>
@endif
