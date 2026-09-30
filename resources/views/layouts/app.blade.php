<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bank Service Portal') - NGDRS Integration</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite with Fallback CDN for instant high-fidelity styling) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        },
                        sidebar: {
                            DEFAULT: '#0e111a',
                            card: '#161924',
                            active: '#1e2235',
                            border: '#24283b'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="h-full flex text-slate-800 antialiased overflow-hidden">

    <!-- Left Dark Sidebar matching screenshots -->
    <aside class="w-64 bg-[#0e111a] flex flex-col justify-between flex-shrink-0 z-20 shadow-2xl border-r border-slate-800">
        <div>
            <!-- Portal Header / Brand -->
            <div class="px-6 py-6 border-b border-slate-800/80">
                <a href="{{ route('dashboard') }}" class="block">
                    <span class="text-xl font-bold tracking-tight text-white block">NGDRS</span>
                    <span class="text-xs font-medium text-slate-400 block mt-0.5 tracking-wide">Bank Portal</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="px-3 py-6 space-y-1.5 text-sm">
                <!-- Dashboard (Common) -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#1e2235] text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('dashboard') ? 'text-orange-500' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                @if($isMasterAdmin)
                    <!-- Master Admin Only: Branches -->
                    <a href="{{ route('branches.index') }}" 
                       class="flex items-center px-4 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('branches.*') ? 'bg-[#1e2235] text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('branches.*') ? 'text-orange-500' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Branches
                    </a>

                    <!-- Master Admin Only: Branch Users (Authorized Officers) -->
                    <a href="{{ route('branch-users.index') }}" 
                       class="flex items-center px-4 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('branch-users.*') ? 'bg-[#1e2235] text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('branch-users.*') ? 'text-orange-500' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Branch users
                    </a>
                @else
                    <!-- Branch Authorized Officer Only: Tracking (Matching Screenshot) -->
                    <a href="{{ route('tracking.index') }}" 
                       class="flex items-center px-4 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('tracking.*') ? 'bg-[#1e2235] text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('tracking.*') ? 'text-orange-500' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Tracking
                    </a>
                @endif

                <!-- Settings (Common) -->
                <a href="{{ route('settings.index') }}" 
                   class="flex items-center px-4 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('settings.*') ? 'bg-[#1e2235] text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('settings.*') ? 'text-orange-500' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </nav>
        </div>

        <!-- Bottom User / Logout Card matching screenshot -->
        <div class="p-4 border-t border-slate-800/80">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-[#f95721] text-white font-bold flex items-center justify-center text-xs shadow-md">
                    @if($isMasterAdmin)
                        HO
                    @else
                        {{ strtoupper(substr($authBranch->branch_name ?? 'GU', 0, 2)) }}
                    @endif
                </div>
                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate">{{ $authBank->bank_name ?? 'Assam Gramin Bank' }}</p>
                    <p class="text-[11px] text-slate-400 truncate">
                        @if($isMasterAdmin)
                            Head Office - Admin
                        @else
                            Branch: {{ $authBranch->branch_name ?? 'Guwahati Kamrup' }}
                        @endif
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center text-xs text-slate-400 hover:text-white transition-colors duration-150 py-1">
                    <span class="mr-1.5 font-mono">→</span> Log out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-[#fafafa]">
        <!-- Top App Bar -->
        <header class="h-14 border-b border-slate-200/80 bg-white flex items-center justify-between px-8 z-10 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <h1 class="text-base font-semibold text-slate-900 tracking-tight">@yield('page_title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>PostgreSQL 18 Connected</span>
                    <span class="text-slate-300">|</span>
                    <span class="font-mono text-slate-600">AES-256 SSO Ready</span>
                </div>

                <div class="w-7 h-7 rounded-full bg-orange-100 text-orange-700 font-bold text-xs flex items-center justify-center border border-orange-200">
                    {{ $isMasterAdmin ? 'HO' : 'AO' }}
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-8 mt-4 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="mx-8 mt-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-medium text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ session('error') ?? $errors->first() }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">✕</button>
            </div>
        @endif

        <!-- Main Body Scrollable -->
        <main class="flex-1 overflow-y-auto px-8 py-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
