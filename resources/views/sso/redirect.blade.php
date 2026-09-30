<!DOCTYPE html>
<html lang="en" class="h-full bg-[#0e111a]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting to NGDRS Portal...</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-center items-center p-6 text-white antialiased">

    <!-- Loading Container -->
    <div class="max-w-md w-full bg-[#161926] rounded-3xl p-8 border border-slate-800 shadow-2xl text-center relative overflow-hidden">
        <!-- Ambient glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Animated Pulse Spinner -->
        <div class="relative w-16 h-16 mx-auto mb-6 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full bg-orange-500/20 animate-ping"></div>
            <div class="w-14 h-14 rounded-2xl bg-[#f95721] flex items-center justify-center shadow-lg shadow-orange-500/30 z-10">
                <svg class="w-7 h-7 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-mono font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            AES-256-CBC AUTHENTICATED POST
        </span>

        <h2 class="text-xl font-bold text-white tracking-tight mb-2">
            Transferring to NGDRS Portal
        </h2>
        <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto mb-5">
            Single Sign-On handshake in progress for <strong>{{ $payload['contact_fname'] }} {{ $payload['contact_lname'] }}</strong> ({{ $payload['bank_user_id'] }}).
        </p>

        <!-- Transfer Status Capsule -->
        <div class="bg-[#0e111a] rounded-2xl p-4 border border-slate-800 text-left text-xs space-y-2 mb-2 font-mono">
            <div class="flex justify-between">
                <span class="text-slate-500">Deed / Bank Trans:</span>
                <span class="text-orange-400 font-bold">{{ $payload['deed_no'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Officer / User Type:</span>
                <span class="text-slate-300">{{ $payload['bank_user_id'] }} (Type: {{ $payload['user_type'] }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Bank & Branch:</span>
                <span class="text-slate-300">{{ $payload['bank_code'] }} • {{ $payload['branch_code'] }}</span>
            </div>
        </div>

        <!-- ZERO-FORM SELF SUBMIT (Field name: "data" matching ngdrs_citizen-dev LoginController@asmsso) -->
        <form id="ssoRedirectForm" method="POST" action="{{ $ssoEndpoint }}">
            <input type="hidden" name="data" value="{{ $encryptedData }}">
            <noscript>
                <button type="submit" class="mt-4 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#f95721]">
                    Click here to proceed if not redirected automatically
                </button>
            </noscript>
        </form>

        <p class="text-[11px] text-slate-500 mt-4">
            Submitting secure token to NGDRS...
        </p>
    </div>

    <script>
        // Zero-form instant POST execution immediately on page load
        (function() {
            function submitForm() {
                var form = document.getElementById('ssoRedirectForm');
                if (form) form.submit();
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', submitForm);
            } else {
                submitForm();
            }
        })();
    </script>
</body>
</html>
