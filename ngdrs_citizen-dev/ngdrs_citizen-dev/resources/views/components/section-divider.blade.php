@props(['dashed' => false, 'color' => 'zinc-300'])

<hr class="border-0 border-t-2 {{ $dashed ? 'border-dashed' : 'border-solid' }} border-{{ $color }} my-8">