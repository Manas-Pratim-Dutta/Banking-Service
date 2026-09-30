@props(['type' => 'button', 'label', 'id' => '', 'class' => '', 'dataAction' => '', 'icon' => false, 'wrapperClass' => ''])

<div class="{{ $wrapperClass }}">
    <button type="{{ $type }}" class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700
                                text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md
                                transition-colors duration-200 cursor-pointer {{ $class }}" id="{{ $id }}" data-action="{{ $dataAction }}">
        {{$label}}
        @if($icon)
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        @endif
    </button>
</div>