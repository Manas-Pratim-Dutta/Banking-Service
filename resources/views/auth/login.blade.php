<!DOCTYPE html>
<html lang="en" class="h-full bg-[#11121a]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - NGDRS Bank Portal</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#f95721',
                            600: '#ea580c',
                            700: '#c2410c',
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
        .hero-glow-sphere {
            position: absolute;
            width: 780px;
            height: 780px;
            left: -260px;
            top: 20%;
            border-radius: 50%;
            background: radial-gradient(circle, #7a431c 0%, #44220b 45%, rgba(17,18,26,0) 75%);
            filter: blur(2px);
            pointer-events: none;
        }
    </style>
</head>
<body class="h-full flex antialiased bg-[#11121a] text-slate-100 overflow-hidden">

    <!-- Left Brand Column with glowing amber sphere matching screenshot -->
    <div class="relative hidden lg:flex lg:w-[58%] flex-col justify-between p-16 overflow-hidden bg-[#11121a]">
        <!-- Ambient Glowing Sphere -->
        <div class="hero-glow-sphere"></div>

        <!-- Top Brand Header -->
        <div class="relative z-10">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-bold tracking-tight text-white uppercase">NGDRS</span>
            </div>
            <p class="text-xs font-medium text-slate-400 mt-0.5 tracking-wide">Assam State Registration</p>
        </div>

        <!-- Main Hero Pitch -->
        <div class="relative z-10 max-w-xl my-auto">
            <h1 class="text-5xl font-extrabold text-white tracking-tight leading-[1.15] mb-6">
                Mortgage deed<br>
                registration,<br>
                simplified.
            </h1>
            <p class="text-slate-300 text-sm leading-relaxed max-w-md">
                Single entry, real-time validation, and automatic charge creation in land records.
            </p>

            <div class="mt-8 flex items-center space-x-6 text-xs text-slate-400">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>PostgreSQL 18</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                    <span>AES-256-CBC SSO</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span>Multi-Tenant Guard</span>
                </div>
            </div>
        </div>

        <!-- Bottom Footer info -->
        <div class="relative z-10 text-[11px] text-slate-500">
            © 2026 National Generic Document Registration System (NGDRS) • State Bank Portal Microservice
        </div>
    </div>

    <!-- Right Login Column matching screenshot -->
    <div class="w-full lg:w-[42%] flex flex-col justify-center items-center p-8 bg-[#f4f6f8] text-slate-800 overflow-y-auto">
        <div class="w-full max-w-md">

            <!-- Flash Error -->
            @if($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-center justify-between">
                    <span>{{ $errors->first() }}</span>
                    <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600">✕</button>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700">
                    {{ session('info') }}
                </div>
            @endif

            <!-- White Login Card -->
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 p-8 border border-slate-100">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-5">
                    Sign in to your bank account
                </h2>

                <!-- Tabs: Master Login vs Branch Login -->
                <div class="grid grid-cols-2 p-1 bg-slate-100/90 rounded-xl mb-6 text-xs font-semibold">
                    <button type="button" 
                            id="tab-master-btn" 
                            onclick="switchTab('master')"
                            class="py-2.5 px-3 rounded-lg text-center transition-all duration-200 bg-white text-slate-900 shadow-xs">
                        <span class="block font-bold">Master Login</span>
                        <span class="block text-[10px] font-normal text-slate-500">Head Branch Admin</span>
                    </button>
                    <button type="button" 
                            id="tab-branch-btn" 
                            onclick="switchTab('branch')"
                            class="py-2.5 px-3 rounded-lg text-center transition-all duration-200 text-slate-500 hover:text-slate-800">
                        <span class="block font-bold">Branch Login</span>
                        <span class="block text-[10px] font-normal text-slate-500">Authorized Officer</span>
                    </button>
                </div>

                <!-- 1. Master Login Form (Head Branch Admin) -->
                <form id="form-master" method="POST" action="{{ route('login.master') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Bank</label>
                        <select name="bank_code" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            @foreach($banks as $b)
                                <option value="{{ $b->bank_code }}">{{ $b->bank_name }} ({{ $b->bank_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Head Office Admin Email</label>
                        <input type="email" 
                               name="email" 
                               id="master_email"
                               value="{{ old('email', 'admin@agb.co.in') }}" 
                               required 
                               placeholder="e.g. admin@agb.co.in" 
                               class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-medium text-slate-600">Password</label>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="master_password"
                               value="password" 
                               required 
                               placeholder="••••••••" 
                               class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <label class="flex items-center text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500 mr-2">
                            Remember session
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition duration-150 shadow-md shadow-orange-500/20 active:scale-[0.99] flex items-center justify-center">
                        Sign in as Master Admin
                    </button>
                </form>

                <!-- 2. Sub Login Form (Authorized Officer / Branch User) -->
                <form id="form-branch" method="POST" action="{{ route('login.branch') }}" class="space-y-4 hidden">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Branch</label>
                        <select name="branch_id" id="branch_select" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}" data-email="{{ $br->contact_email }}">
                                    {{ $br->branch_name }} — {{ $br->district }} ({{ $br->ifsc_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Authorized Officer Email</label>
                        <input type="email" 
                               name="email" 
                               id="branch_email"
                               value="branch.guwahati@agb.co.in" 
                               required 
                               placeholder="e.g. branch.guwahati@agb.co.in" 
                               class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-slate-600 mb-1.5 block">Password</label>
                        <input type="password" 
                               name="password" 
                               id="branch_password"
                               value="password" 
                               required 
                               placeholder="••••••••" 
                               class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <label class="flex items-center text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500 mr-2">
                            Remember session
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition duration-150 shadow-md shadow-orange-500/20 active:scale-[0.99] flex items-center justify-center">
                        Sign in as Authorized Officer
                    </button>
                </form>

                <!-- Quick Demo Fill Section -->
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <p class="text-[11px] font-medium text-slate-400 mb-2">Demo Accounts (Pre-configured):</p>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" 
                                onclick="fillCredentials('master', 'admin@agb.co.in', 'password')"
                                class="text-[11px] px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-md font-mono transition">
                            HO Admin
                        </button>
                        <button type="button" 
                                onclick="fillCredentials('branch', 'branch.guwahati@agb.co.in', 'password')"
                                class="text-[11px] px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-md font-mono transition">
                            Officer Guwahati
                        </button>
                        <button type="button" 
                                onclick="fillCredentials('branch', 'branch.silchar@agb.co.in', 'password')"
                                class="text-[11px] px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-md font-mono transition">
                            Officer Silchar
                        </button>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-xs text-slate-500">
                        New bank on NGDRS? 
                        <a href="mailto:support@ngdrs.gov.in" class="text-orange-600 font-semibold hover:underline">Register here</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        function switchTab(type) {
            const masterBtn = document.getElementById('tab-master-btn');
            const branchBtn = document.getElementById('tab-branch-btn');
            const masterForm = document.getElementById('form-master');
            const branchForm = document.getElementById('form-branch');

            if (type === 'master') {
                masterBtn.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                masterBtn.classList.remove('text-slate-500');
                branchBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                branchBtn.classList.add('text-slate-500');

                masterForm.classList.remove('hidden');
                branchForm.classList.add('hidden');
            } else {
                branchBtn.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                branchBtn.classList.remove('text-slate-500');
                masterBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                masterBtn.classList.add('text-slate-500');

                branchForm.classList.remove('hidden');
                masterForm.classList.add('hidden');
            }
        }

        function fillCredentials(tab, email, pass) {
            switchTab(tab);
            if (tab === 'master') {
                document.getElementById('master_email').value = email;
                document.getElementById('master_password').value = pass;
            } else {
                document.getElementById('branch_email').value = email;
                document.getElementById('branch_password').value = pass;
            }
        }
    </script>
</body>
</html>
