@props([
    'id' => 'global-loader',
    'text' => 'Loading...',
    'subtext' => 'Please wait a moment',
    'fullscreen' => true,
    'size' => 'md',
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => [
            'outer' => 'w-7 h-7 border-2',
            'inner' => 'w-3.5 h-3.5 border',
            'text' => 'text-xs',
            'subtext' => 'text-[10px]',
            'padding' => 'p-3',
        ],
        'md' => [
            'outer' => 'w-12 h-12 border-3',
            'inner' => 'w-6 h-6 border-2',
            'text' => 'text-sm',
            'subtext' => 'text-xs',
            'padding' => 'p-6',
        ],
        'lg' => [
            'outer' => 'w-16 h-16 border-4',
            'inner' => 'w-8 h-8 border-3',
            'text' => 'text-base',
            'subtext' => 'text-xs',
            'padding' => 'p-8',
        ],
    ][$size] ?? [
        'outer' => 'w-12 h-12 border-3',
        'inner' => 'w-6 h-6 border-2',
        'text' => 'text-sm',
        'subtext' => 'text-xs',
        'padding' => 'p-6',
    ];
@endphp

@if ($fullscreen)
    <!-- Uniform Global / Fullscreen Loader -->
    <div id="{{ $id }}"
        class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300 z-50 {{ $class }}"
        role="status" aria-live="polite">
        <div
            class="loader-card flex flex-col items-center justify-center {{ $sizeClasses['padding'] }} bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-100/90 max-w-sm mx-4 min-w-[220px] text-center transform scale-95 transition-transform duration-300">
            <!-- Dual-ring Spinner -->
            <div class="relative flex items-center justify-center" aria-hidden="true">
                <div
                    class="{{ $sizeClasses['outer'] }} rounded-full border-orange-200 border-t-orange-500 animate-spin">
                </div>
                <div class="absolute {{ $sizeClasses['inner'] }} rounded-full border-orange-100 border-b-orange-600 animate-spin"
                    style="animation-direction: reverse; animation-duration: 0.75s;">
                </div>
            </div>

            <!-- Loader Text Content -->
            <div class="mt-3.5 space-y-0.5">
                <p id="{{ $id }}-text" data-default-text="{{ $text }}"
                    class="{{ $sizeClasses['text'] }} font-semibold text-gray-800 tracking-tight">
                    {{ $text }}
                </p>
                @if ($subtext)
                    <p id="{{ $id }}-subtext" data-default-subtext="{{ $subtext }}"
                        class="{{ $sizeClasses['subtext'] }} text-gray-400 font-normal">
                        {{ $subtext }}
                    </p>
                @endif
            </div>
        </div>
    </div>
@else
    <!-- Uniform Inline / Scoped Loader -->
    <div id="{{ $id }}"
        class="flex flex-col items-center justify-center {{ $sizeClasses['padding'] }} text-center {{ $class }}"
        role="status" aria-live="polite">
        <div class="relative flex items-center justify-center" aria-hidden="true">
            <div
                class="{{ $sizeClasses['outer'] }} rounded-full border-orange-200 border-t-orange-500 animate-spin">
            </div>
            <div class="absolute {{ $sizeClasses['inner'] }} rounded-full border-orange-100 border-b-orange-600 animate-spin"
                style="animation-direction: reverse; animation-duration: 0.75s;">
            </div>
        </div>

        @if ($text)
            <div class="mt-3 space-y-0.5">
                <p id="{{ $id }}-text" data-default-text="{{ $text }}"
                    class="{{ $sizeClasses['text'] }} font-semibold text-gray-800 tracking-tight">
                    {{ $text }}
                </p>
                @if ($subtext)
                    <p id="{{ $id }}-subtext" data-default-subtext="{{ $subtext }}"
                        class="{{ $sizeClasses['subtext'] }} text-gray-400 font-normal">
                        {{ $subtext }}
                    </p>
                @endif
            </div>
        @endif
    </div>
@endif
