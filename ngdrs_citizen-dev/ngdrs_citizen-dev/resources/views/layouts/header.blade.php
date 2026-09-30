<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-base-path" content="{{ rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?: '', '/') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- Editor Links -->
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <!-- ----- -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @stack('page-styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
</head>

<body class="bg-zinc-100">
    <section class="header bg-white">
        <div class="top-wrapper px-8 py-1.5 flex align-center justify-between bg-branddark text-xs text-gray-50">
            <div id="liveDateTime"></div>
            <div>| &nbsp; English &nbsp; |</div>
        </div>
        <div class="banner px-8 py-5 flex gap-8 ">
            <div class="banner-img"><img src="{{ asset('images/banner-img.png') }}" alt="" width="112" height="112">
            </div>
            <div class="flex flex-col justify-center gap-2">
                <div class=" font-medium text-[2.4rem]">
                    <h1 class="tracking-wider text-zinc-800">MATi Document Registration</h1>
                </div>
                <div class="flex text-xs text-zinc-700">
                    <div>
                        <p>Department of Land Resources - Government of India</p>
                    </div> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <div>
                        <p>Revenue and D.M. (Registration) Department, Assam</p>
                    </div>
                </div>
            </div>
        </div>
    </section>