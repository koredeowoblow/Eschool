@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, warning
    'loadingText' => 'Processing...',
])

@php
    $baseClasses = 'px-4 py-2 rounded-lg transition-all inline-flex items-center justify-center gap-2 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-400',
    ];

    $colorClasses = $variants[$variant] ?? $variants['primary'];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "$baseClasses $colorClasses"]) }}
    :disabled="loading"
    :class="loading ? 'opacity-50 cursor-not-allowed' : ''"
>
    <span x-show="!loading" class="inline-flex items-center gap-2">
        {{ $slot }}
    </span>
    
    <span x-show="loading" class="inline-flex items-center gap-2" style="display: none;">
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ $loadingText }}
    </span>
</button>
