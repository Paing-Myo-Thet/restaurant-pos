@props(['status'])

@php
    $styles = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'sent' => 'bg-blue-100 text-blue-800',
        'preparing' => 'bg-orange-100 text-orange-800',
        'ready' => 'bg-green-100 text-green-800',
        'served' => 'bg-gray-100 text-gray-700',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->class(['inline-block rounded-full px-3 py-1 text-xs font-semibold', $styles[$status] ?? 'bg-gray-100 text-gray-700']) }}>
    {{ $status === 'sent' ? 'Sent to kitchen' : ucfirst($status) }}
</span>
