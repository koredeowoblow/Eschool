@props(['id', 'title'])

<div 
  x-data="{ 
    open: false,
    previousFocus: null,
  }"
  x-on:open-modal.window="if ($event.detail === '{{ $id }}') { previousFocus = $event.target; open = true; $nextTick(() => $focus.within($refs.modal).first()); }"
  x-on:close-modal.window="if ($event.detail === '{{ $id }}') open = false;"
  @keydown.escape.window="open = false"
>
  <!-- Modal Overlay -->
  <div 
    x-show="open" 
    @click.self="open = false"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 transition-opacity"
    x-transition.opacity.duration.300ms
    style="display: none;"
  >
    <!-- Modal Dialog -->
    <div 
      x-show="open"
      x-ref="modal"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      @keydown.tab="$event.shiftKey ? $focus.first() : $focus.last()"
      class="bg-white rounded-2xl shadow-xl w-[95%] sm:w-[500px] max-h-[90vh] flex flex-col"
      role="dialog"
      aria-modal="true"
      aria-labelledby="modal-title-{{ $id }}"
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b">
        <h2 id="modal-title-{{ $id }}" class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
        <button 
          @click="open = false"
          aria-label="Close modal"
          class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <!-- Content -->
      <div class="p-4 overflow-y-auto">
        {{ $slot }}
      </div>
      
    </div>
  </div>
</div>
