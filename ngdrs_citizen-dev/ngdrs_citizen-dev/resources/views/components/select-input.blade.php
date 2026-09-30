@props(['id' => null, 'label', 'name', 'options' => [], 'required' => false, 'selected' => null])

<div class="mb-5">
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="relative">
        <select @isset($id) id="{{ $id ?? '' }}" @endisset name="{{ $name }}" {{ $attributes->merge(['class' => 'tom-select w-full']) }} @error($name) class="border-red-400" @enderror>
            <option value="" disabled selected>Select</option>
            @if($slot->isNotEmpty())
                {{ $slot }}
            @else
                @foreach($options as $value => $text)
                    <option value="{{ $value }}" @selected(old($name, $selected) == $value)>
                        {{ $text }}
                    </option>
                @endforeach
            @endif
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
    @error($name)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>