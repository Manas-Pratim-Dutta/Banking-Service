<!-- OBSOLETE -->
@include('layouts.header')

<div class="min-h-screen flex">
    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col">
        <main class="flex-1 p-2 m-4">
            {{ $slot }}
        </main>
        @include('layouts.footer')
    </div>
</div>