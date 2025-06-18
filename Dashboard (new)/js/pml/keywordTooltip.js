// /js/pml/keywordTooltip.js

let activeKeyword = null;

export function initKeywordTooltips(context = document) {
    Array.from(context.querySelectorAll(".keyword")).forEach(keyword => {
        // Remove any existing handler
        if (keyword._tooltipHandler) {
            keyword.removeEventListener('click', keyword._tooltipHandler);
        }
        
        // Create and store the handler
        keyword._tooltipHandler = (event) => handleKeywordClick(event, keyword);
        keyword.addEventListener("click", keyword._tooltipHandler);
    });
}

function handleKeywordClick(event, keyword) {
    console.log("Keyword clicked:", keyword.innerText);
    activeKeyword = keyword;
    event.stopPropagation();

    // Remove popup if already visible for this keyword
    const existingPopup = document.querySelector(".keyword-popup");
    console.log("Existing popup found:", existingPopup);

    if (existingPopup && existingPopup.dataset.keyword === keyword.innerText) {
        console.log("Removing existing popup for same keyword");
        existingPopup.remove();
        keyword.classList.remove("highlighted");
        return;
    }

    // Clean up any previous popup and highlights
    if (existingPopup) {
        existingPopup.remove();
    }
    document.querySelectorAll(".keyword").forEach(k => k.classList.remove("highlighted"));

    // Create popup if glossary definition exists
    const definition = keyword.getAttribute("data-glossary");
    console.log("Definition found:", definition);

    if (!definition) return;

    console.log("About to create popup...");
    
    try {
        const popup = createPopup(keyword.innerText, definition);
        console.log("Popup created successfully:", popup);
        console.log("Popup HTML:", popup.outerHTML);
        
        console.log("About to append to body...");
        document.body.appendChild(popup);
        console.log("Popup appended to body");

        // Show and position the popup
        popup.style.display = "block";
        console.log("Popup display set to block");
        
        repositionPopup();
        console.log("Popup repositioned");
        
        keyword.classList.add("highlighted");

        // Stop propagation for the popup itself
        popup.addEventListener("click", e => e.stopPropagation());

        // Click outside to close
        document.addEventListener("click", function closePopup(e) {
            if (!popup.contains(e.target) && !keyword.contains(e.target)) {
                popup.remove();
                keyword.classList.remove("highlighted");
                document.removeEventListener("click", closePopup);
            }
        }, { once: true });

        // Close button
        popup.querySelector(".popup-close").addEventListener("click", () => {
            popup.remove();
            keyword.classList.remove("highlighted");
        });

        // Typeset MathJax if present
        if (window.MathJax) {
            window.MathJax.typeset && window.MathJax.typeset();
            window.MathJax.typesetPromise && window.MathJax.typesetPromise();
        }

        // Reposition on resize
        window.addEventListener("resize", repositionPopup);
        
        console.log("Popup in DOM:", document.body.contains(popup));
        console.log("Popup computed style:", window.getComputedStyle(popup).display);
        
    } catch (error) {
        console.error("Error creating popup:", error);
    }
}

function createPopup(keywordText, definition) {
    console.log("Creating popup for:", keywordText, "with definition:", definition);
    
    const popupElement = document.createElement("div");
    popupElement.className = "keyword-popup";
    popupElement.dataset.keyword = keywordText;
    popupElement.innerHTML = `
        <span class="popup-close"><i class="fa-solid fa-circle-xmark"></i></span>
        <div class="popup-content">
            <p>${definition.replace(/\n/g, "<br>")}</p>
        </div>
    `;

    console.log("Popup element created:", popupElement);
    return popupElement;
}

function repositionPopup() {
    const popup = document.querySelector(".keyword-popup");
    if (!popup || !activeKeyword) return;

    const rect = activeKeyword.getBoundingClientRect();
    const popupWidth = popup.offsetWidth;
    const windowWidth = window.innerWidth;

    // Assume a centered container of max width 768px
    const contentMaxWidth = 768;
    const contentLeft = (windowWidth - contentMaxWidth) / 2;
    const contentRight = contentLeft + contentMaxWidth;

    // Center horizontally on the keyword
    let keywordCenter = rect.left + rect.width / 2 + window.scrollX;
    let leftPosition = keywordCenter - popupWidth / 2;

    // Clamp to container
    if (leftPosition < contentLeft) leftPosition = contentLeft + 8;
    else if (leftPosition + popupWidth > contentRight) leftPosition = contentRight - popupWidth - 8;

    // Place just below keyword
    let topPosition = rect.bottom + window.scrollY + 8;

    popup.style.left = `${leftPosition}px`;
    popup.style.top = `${topPosition}px`;
}