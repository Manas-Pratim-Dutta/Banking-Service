const STEP_LABELS = [
    "General Info",
    "Property",
    "Party",
    "Witness",
    "Identifier",
    "Fee Calculation",
    "Upload Document",
    "eDeed",
    "Appointment",
];

const ROUTE_TO_STEP: Record<string, number> = {
    "/general-info": 0,
    "/property": 1,
    "/party": 2,
    "/witness": 3,
    "/identifier": 4,
    "/fee-calculation": 5,
    "/edeed": 6,
    "/upload-document": 7,
    "/appointment": 8,
};

function setNgdrsStep(activeIndex: number): void {
    const steps = document.querySelectorAll(".ngdrs-step");
    const progressLine = document.getElementById("ngdrs-progress-line");
    const currentLabel = document.getElementById("ngdrs-current-label");
    const stepCount = document.getElementById("ngdrs-step-count");
    const total = steps.length;

    steps.forEach((step, index) => {
        const circle = step.querySelector(".step-circle");
        const label = step.querySelector(".step-label");

        step.querySelector(".active-indicator-pill")?.remove();
        circle?.querySelector(".active-pulse")?.remove();

        if (!circle || !label) return;

        circle.className = circle.className
            .replace(/bg-\S+|border-\S+|text-\S+|ring-\S+|shadow-\S+|relative/g, "")
            .trim();
        label.className = label.className
            .replace(/text-\S+|font-\S+/g, "")
            .trim();

        if (index < activeIndex) {
            circle.classList.add(
                "bg-orange-500",
                "border-orange-500",
                "text-white",
                "shadow-sm",
            );
            label.classList.add("text-orange-500");
            circle.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>`;
        } else if (index === activeIndex) {
            circle.classList.add(
                "relative",
                "bg-orange-500",
                "border-orange-500",
                "text-white",
                "ring-4",
                "ring-orange-100",
                "shadow-md",
                "shadow-orange-300/60",
            );
            label.classList.add("text-orange-600", "font-bold");

            const pulse = document.createElement("span");
            pulse.className = "active-pulse absolute -top-1 -right-1 flex h-2.5 w-2.5 pointer-events-none";
            pulse.innerHTML = `
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-500 border border-white"></span>
            `;
            circle.appendChild(pulse);

            const pill = document.createElement("span");
            pill.className = "active-indicator-pill mt-1 px-1.5 py-0.5 text-[9px] font-bold text-orange-700 bg-orange-100 rounded-full leading-none tracking-tight shadow-2xs";
            pill.textContent = "Current";
            step.appendChild(pill);
        } else {
            circle.classList.add(
                "bg-white",
                "border-orange-200",
                "text-orange-300",
            );
            label.classList.add("text-gray-400");
        }
    });

    const progress = activeIndex === 0 ? 0 : (activeIndex / (total - 1)) * 100;
    if (progressLine) progressLine.style.width = `${progress}%`;
    if (currentLabel) currentLabel.textContent = STEP_LABELS[activeIndex];
    if (stepCount) stepCount.textContent = `Step ${activeIndex + 1} of ${total}`;
}

export function initProgressSteps(): void {
    const basePath = document.querySelector<HTMLMetaElement>(
        'meta[name="app-base-path"]',
    )?.content ?? "";
    const pathname = window.location.pathname.replace(/\/$/, "") || "/";
    const path = basePath && pathname.startsWith(`${basePath}/`)
        ? pathname.slice(basePath.length)
        : pathname === basePath ? "/" : pathname;

    setNgdrsStep(ROUTE_TO_STEP[path] ?? 0);
}