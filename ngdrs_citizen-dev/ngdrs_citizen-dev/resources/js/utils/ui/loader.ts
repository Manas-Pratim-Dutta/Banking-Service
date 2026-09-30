let requestCount = 0;

function getLoader(id: string = "global-loader"): HTMLElement | null {
    return document.getElementById(id);
}

function getLoaderText(id: string = "global-loader"): HTMLElement | null {
    return document.getElementById(`${id}-text`);
}

function getLoaderSubtext(id: string = "global-loader"): HTMLElement | null {
    return document.getElementById(`${id}-subtext`);
}

export function showLoader(message?: string, subtext?: string, loaderId: string = "global-loader") {
    requestCount++;

    const loader = getLoader(loaderId);
    if (!loader) return;

    if (message) {
        const textEl = getLoaderText(loaderId);
        if (textEl) {
            textEl.textContent = message;
        }
    }

    if (subtext) {
        const subtextEl = getLoaderSubtext(loaderId);
        if (subtextEl) {
            subtextEl.textContent = subtext;
            subtextEl.classList.remove("hidden");
        }
    }

    loader.classList.remove("opacity-0", "pointer-events-none");
    loader.classList.add("opacity-100");

    const card = loader.querySelector(".loader-card");
    if (card) {
        card.classList.remove("scale-95");
        card.classList.add("scale-100");
    }
}

export function hideLoader(loaderId: string = "global-loader") {
    requestCount--;

    if (requestCount <= 0) {
        requestCount = 0;

        const loader = getLoader(loaderId);
        if (!loader) return;

        loader.classList.remove("opacity-100");
        loader.classList.add("opacity-0", "pointer-events-none");

        const card = loader.querySelector(".loader-card");
        if (card) {
            card.classList.remove("scale-100");
            card.classList.add("scale-95");
        }

        // Reset text back to default
        const textEl = getLoaderText(loaderId);
        if (textEl) {
            const defaultText = textEl.getAttribute("data-default-text") || "Loading...";
            textEl.textContent = defaultText;
        }

        const subtextEl = getLoaderSubtext(loaderId);
        if (subtextEl) {
            const defaultSubtext = subtextEl.getAttribute("data-default-subtext");
            if (defaultSubtext) {
                subtextEl.textContent = defaultSubtext;
                subtextEl.classList.remove("hidden");
            }
        }
    }
}