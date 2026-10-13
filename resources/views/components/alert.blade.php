@props(['type' => 'success', 'title' => null])

@php
    $styles = [
        'success' => ['ring-green-200 bg-green-50', 'bg-green-100 text-green-700', 'text-green-900', 'text-green-800', 'bg-green-500', 'Success'],
        'error' => ['ring-red-200 bg-red-50', 'bg-red-100 text-red-700', 'text-red-900', 'text-red-800', 'bg-red-500', 'Something went wrong'],
        'warning' => ['ring-amber-200 bg-amber-50', 'bg-amber-100 text-amber-700', 'text-amber-900', 'text-amber-800', 'bg-amber-500', 'Heads up'],
    ];
    [$box, $iconBox, $titleColor, $textColor, $bar, $defaultTitle] = $styles[$type] ?? $styles['success'];

    // Success messages fade out on their own; errors stay until dismissed.
    $autoDismiss = $type === 'success';
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}"
     data-alert
     @if ($autoDismiss) data-auto-dismiss="5000" @endif
     {{ $attributes->class(['relative mb-6 flex items-start gap-3 overflow-hidden rounded-xl p-4 shadow-sm ring-1 transition-all duration-300', $box]) }}>

    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $iconBox }}">
        @if ($type === 'success')
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.6l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/>
            </svg>
        @elseif ($type === 'error')
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.7 7.3a1 1 0 0 0-1.4 1.4L8.6 10l-1.3 1.3a1 1 0 1 0 1.4 1.4l1.3-1.3 1.3 1.3a1 1 0 0 0 1.4-1.4L11.4 10l1.3-1.3a1 1 0 0 0-1.4-1.4L10 8.6 8.7 7.3Z" clip-rule="evenodd"/>
            </svg>
        @else
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.3 2.9a2 2 0 0 1 3.4 0l6.3 11A2 2 0 0 1 16.3 17H3.7A2 2 0 0 1 2 13.9l6.3-11ZM10 7a1 1 0 0 0-1 1v3a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>
        @endif
    </div>

    <div class="min-w-0 flex-1 pt-0.5">
        <p class="text-sm font-semibold {{ $titleColor }}">{{ $title ?? $defaultTitle }}</p>
        <div class="mt-0.5 text-sm {{ $textColor }}">{{ $slot }}</div>
    </div>

    <button type="button" data-alert-close aria-label="Dismiss"
            class="-m-1 rounded-lg p-1.5 {{ $textColor }} opacity-60 transition hover:bg-black/5 hover:opacity-100">
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M6.3 5.3a1 1 0 0 0-1.4 1.4L8.6 10l-3.7 3.3a1 1 0 1 0 1.4 1.4L10 11.4l3.3 3.3a1 1 0 0 0 1.4-1.4L11.4 10l3.3-3.3a1 1 0 0 0-1.4-1.4L10 8.6 6.3 5.3Z"/>
        </svg>
    </button>

    @if ($autoDismiss)
        {{-- Countdown bar for the auto-dismiss --}}
        <div class="absolute bottom-0 left-0 h-0.5 w-full origin-left {{ $bar }}" data-alert-progress></div>
    @endif
</div>
