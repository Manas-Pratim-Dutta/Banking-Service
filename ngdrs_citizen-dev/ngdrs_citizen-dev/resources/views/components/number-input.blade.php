@props([
    'label',
    'name',
    'placeholder' => '',
    'required' => false,
    'wrapperClass' => '',
    'readonly' => false,
    'value'=>''
])

<div class="{{ $wrapperClass }}">
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input 
        type="number"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"

        @if($readonly) readonly @endif

        {{ $attributes->merge([
            'class' => '
                w-full border rounded-lg text-sm px-4 py-2.5
                focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent read-only:bg-gray-100
        read-only:text-gray-500
        read-only:cursor-not-allowed
        read-only:border-gray-300
                ' .
                ($readonly
                    ? 'bg-gray-100 text-gray-500 border-gray-200 cursor-not-allowed'
                    : 'bg-white text-gray-700 border-gray-300')
            ])
        }}
    >

    @error($name)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>