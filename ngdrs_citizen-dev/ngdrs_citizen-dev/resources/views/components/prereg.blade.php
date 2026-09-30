@props([
    'preRegNo'
])

<div class="flex items-center gap-3">
    <h2 class="text-xs font-semibold text-gray-500">
        PRE REGISTRATION NO.
    </h2>

    <span
        class="text-gray-800 font-semibold"
        data-pre-reg-no="{{ $preRegNo }}"
    >
        {{ $preRegNo }}
    </span>
</div>  