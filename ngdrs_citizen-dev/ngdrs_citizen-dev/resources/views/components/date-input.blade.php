@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'id' => '',
    'required' => false
])

<div class="">
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <input 
        type="date"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($name,$value) }}"
        {{ $attributes->merge(['class' => 'w-full border border-gray-300 rounded-lg text-sm px-4 py-2.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent']) }}
        @error($name) class="border-red-400" @enderror
    >
    @error($name)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>