@props([
'title',
'icon'=> null,
'subtitle'=> null,
'iconColor' => 'color-primary'
])


<div class="flex items-center gap-3 mb-6">

    @if($icon)
    <img
        src="{{ asset($icon) }}"
        class="w-7 h-7.5"
        alt="Document Icon" />
    @endif

    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? ''}}</h2>
     @if($subtitle)
    <span class="text-gray-500 font-semibold">{{ $subtitle ?? ''}}</span> 
     @endif
</div>