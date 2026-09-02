@props(['title', 'message', 'buttonText' => null, 'buttonAction' => null])

<div class="flex flex-col items-center justify-center py-12 px-4 animate-slide-in-right">
    <!-- Illustration -->
    <svg class="w-20 h-20 text-gray-300  mb-6 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    
    <!-- Message -->
    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $title }}</h3>
    <p class="text-gray-500  text-center  mb-6 max-w-sm">
        {{ $message }}
    </p>
    
    @if($buttonText)
        <button 
            @if($buttonAction) @click="{{ $buttonAction }}" @endif
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 font-medium"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ $buttonText }}
        </button>
    @endif
</div>
