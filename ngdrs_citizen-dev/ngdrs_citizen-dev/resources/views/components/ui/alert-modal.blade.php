@props([
    'id',
    'title' => '',
    'description' => '',
    'confirmText' => 'Yes',
    'cancelText' => 'No',
    'type' => 'info' // presets: 'info', 'warning', 'success', 'logout', 'disclaimer'
])

<div id="{{ $id }}" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300" onclick="window.closeAlertModal('{{ $id }}', false)"></div>
    
    <!-- Modal Card -->
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0 border border-gray-100 flex flex-col items-center text-center">
        
        <!-- Close Button -->
        <button type="button" onclick="window.closeAlertModal('{{ $id }}', false)" class="absolute top-6 right-6 text-orange-500 hover:scale-110 active:scale-95 transition-transform cursor-pointer focus:outline-none">
            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="16" r="14" fill="#F97316"/>
                <path d="M11 11L21 21M21 11L11 21" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </button>

        <!-- Illustration Slot or Preset -->
        <div class="mb-6 w-full flex justify-center">
            @if($type === 'logout' || $type === 'info')
                <!-- Sad Screen / Browser Mockup SVG -->
                <svg width="180" height="150" viewBox="0 0 180 150" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-orange-500">
                    <!-- Desk/Floor Shadow -->
                    <ellipse cx="90" cy="140" rx="65" ry="4" fill="#E4E4E7" opacity="0.6"/>
                    
                    <!-- Monitor Stand -->
                    <path d="M82 110 L98 110 L102 138 L78 138 Z" fill="#D4D4D8"/>
                    <rect x="68" y="135" width="44" height="4" rx="2" fill="#A1A1AA"/>

                    <!-- Monitor Frame -->
                    <rect x="36" y="24" width="108" height="76" rx="6" fill="#F4F4F5" stroke="#E4E4E7" stroke-width="2"/>
                    <rect x="40" y="28" width="100" height="68" rx="4" fill="#E4E4E7"/>
                    
                    <!-- Browser Window (floating) -->
                    <g filter="url(#drop-shadow)">
                        <rect x="48" y="32" width="84" height="52" rx="4" fill="white" stroke="#FDA4AF" stroke-width="1.5"/>
                        <!-- Browser Header -->
                        <path d="M48 35.5 C48 33.5 49.5 32 51.5 32 H128.5 C130.5 32 132 33.5 132 35.5 V40 H48 Z" fill="#FEE2E2"/>
                        <!-- Dots -->
                        <circle cx="54" cy="36" r="1.5" fill="#EF4444"/>
                        <circle cx="59" cy="36" r="1.5" fill="#FBBF24"/>
                        <circle cx="64" cy="36" r="1.5" fill="#10B981"/>
                        
                        <!-- File/Page with Sad Icon -->
                        <rect x="74" y="46" width="32" height="32" rx="3" fill="none" stroke="#F97316" stroke-width="1.5" stroke-dasharray="2 2"/>
                        <!-- Sad Face Inside Document -->
                        <circle cx="90" cy="58" r="4" fill="none" stroke="#F97316" stroke-width="1.5"/>
                        <circle cx="88" cy="57" r="0.75" fill="#F97316"/>
                        <circle cx="92" cy="57" r="0.75" fill="#F97316"/>
                        <path d="M87.5 61 C88.5 60 91.5 60 92.5 61" stroke="#F97316" stroke-width="1" stroke-linecap="round"/>
                    </g>
                    
                    <defs>
                        <filter id="drop-shadow" x="42" y="28" width="96" height="64" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity="0.1"/>
                        </filter>
                    </defs>
                </svg>
            @elseif($type === 'disclaimer' || $type === 'warning')
                <!-- Disclaimer / Caution SVG -->
                <svg width="180" height="150" viewBox="0 0 180 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="90" cy="140" rx="65" ry="4" fill="#E4E4E7" opacity="0.6"/>
                    
                    <!-- Shield Background -->
                    <path d="M90 20 L135 38 C135 80 108 115 90 125 C72 115 45 80 45 38 Z" fill="#FFF7ED" stroke="#F97316" stroke-width="3" stroke-linejoin="round"/>
                    
                    <!-- Exclamation point inside shield -->
                    <path d="M90 45 V80" stroke="#F97316" stroke-width="6" stroke-linecap="round"/>
                    <circle cx="90" cy="98" r="4.5" fill="#F97316"/>
                </svg>
            @elseif($type === 'success')
                <!-- Success Checkmark SVG -->
                <svg width="180" height="150" viewBox="0 0 180 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="90" cy="140" rx="65" ry="4" fill="#E4E4E7" opacity="0.6"/>
                    <circle cx="90" cy="70" r="45" fill="#DCFCE7" stroke="#22C55E" stroke-width="3"/>
                    <path d="M72 70 L84 82 L108 58" stroke="#22C55E" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @endif
        </div>

        <!-- Title -->
        <h3 class="text-2xl font-bold text-gray-900 mb-2 id-title-el">{{ $title }}</h3>

        <!-- Description / Slotted Content -->
        <div class="text-gray-500 text-sm leading-relaxed mb-8 id-desc-el">
            @if($slot->isEmpty())
                {{ $description }}
            @else
                {{ $slot }}
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 w-full">
            <button type="button" onclick="window.closeAlertModal('{{ $id }}', false)" class="flex-1 py-3 px-6 rounded-xl border border-orange-500 text-orange-500 font-semibold hover:bg-orange-50 active:scale-95 transition-all duration-200 cursor-pointer focus:outline-none text-center id-cancel-btn">
                {{ $cancelText }}
            </button>
            <button type="button" onclick="window.closeAlertModal('{{ $id }}', true)" class="flex-1 py-3 px-6 rounded-xl bg-orange-500 text-white font-semibold hover:bg-orange-600 active:bg-orange-700 active:scale-95 transition-all duration-200 cursor-pointer focus:outline-none text-center id-confirm-btn">
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>

@once
<script>
    window.showAlertModal = function(id, options = {}) {
        const modal = document.getElementById(id);
        if (!modal) return Promise.resolve(false);

        const card = modal.querySelector('.relative');

        if (options.title) {
            const titleEl = modal.querySelector('.id-title-el');
            if (titleEl) titleEl.innerHTML = options.title;
        }
        if (options.description) {
            const descEl = modal.querySelector('.id-desc-el');
            if (descEl) descEl.innerHTML = options.description;
        }
        if (options.confirmText) {
            const confirmBtn = modal.querySelector('.id-confirm-btn');
            if (confirmBtn) confirmBtn.textContent = options.confirmText;
        }
        if (options.cancelText) {
            const cancelBtn = modal.querySelector('.id-cancel-btn');
            if (cancelBtn) cancelBtn.textContent = options.cancelText;
        }

        modal.classList.remove('hidden');
        
        // Use requestAnimationFrame for smooth CSS transitions
        requestAnimationFrame(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        });

        return new Promise((resolve) => {
            modal._resolveAlertModal = resolve;
        });
    };

    window.closeAlertModal = function(id, result) {
        const modal = document.getElementById(id);
        if (!modal) return;

        const card = modal.querySelector('.relative');

        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            if (typeof modal._resolveAlertModal === 'function') {
                modal._resolveAlertModal(result);
                modal._resolveAlertModal = null;
            }
        }, 200);
    };
</script>
@endonce
