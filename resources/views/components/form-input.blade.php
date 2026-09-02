@props(['name', 'label', 'type' => 'text', 'placeholder' => ''])

<div class=" mb-6 ">
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        x-model="form.{{ $name }}"
        @blur="validateField('{{ $name }}')"
        :class="errors.{{ $name }} ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'"
        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 transition-all"
        placeholder="{{ $placeholder }}"
        {{ $attributes }}
    >
    
    <!-- Field-Level Error Message -->
    <div x-show="errors.{{ $name }} && touched.{{ $name }}" style="display:none;" class="mt-1 flex items-center gap-1 text-sm text-red-600">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        <span x-text="errors.{{ $name }}"></span>
    </div>
    
    <!-- Success State -->
    <div x-show="!errors.{{ $name }} && touched.{{ $name }} && form.{{ $name }}" style="display:none;" class="mt-1 flex items-center gap-1 text-sm text-green-600">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        Looks good!
    </div>
</div>
