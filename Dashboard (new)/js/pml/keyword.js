// Global variable to track the active keyword
let activeKeyword;

function initKeywordTooltips() {
    document.querySelectorAll(".keyword").forEach(keyword => {
        keyword.onclick = null; // Remove any previous
        keyword.addEventListener("click", event => handleKeywordClick(event, keyword));
    });
}

// Handle keyword click event
function handleKeywordClick(event, keyword) {
    activeKeyword = keyword;
    event.stopPropagation();

    // Remove existing popup if it matches the clicked keyword
    const existingPopup = document.querySelector(".keyword-popup");
    if (existingPopup && existingPopup.dataset.keyword === keyword.innerText) {
        existingPopup.remove();
        keyword.classList.remove("highlighted");
        return;
    }

    // Clean up any existing popup and highlighted keywords
    if (existingPopup) {
        existingPopup.remove();
    }
    document.querySelectorAll(".keyword").forEach(k => k.classList.remove("highlighted"));

    // Get definition and create popup
    const definition = keyword.getAttribute("data-glossary");
    if (!definition) return;

    const popup = createPopup(keyword.innerText, definition);
    document.body.appendChild(popup);

    // Position and display popup
    popup.style.display = "block";
    repositionPopup();
    keyword.classList.add("highlighted");

    // Prevent popup click from propagating
    popup.addEventListener("click", e => e.stopPropagation());

    // Close popup when clicking outside
    document.addEventListener("click", function closePopup(e) {
        if (!popup.contains(e.target) && !keyword.contains(e.target)) {
            popup.remove();
            keyword.classList.remove("highlighted");
            document.removeEventListener("click", closePopup);
        }
    }, { once: true });

    // Close popup when clicking the close button
    popup.querySelector(".popup-close").addEventListener("click", () => {
        popup.remove();
        keyword.classList.remove("highlighted");
    });

    // Typeset MathJax if available
    if (window.MathJax) {
        MathJax.typeset();
    }

    // Reposition popup on window resize
    window.addEventListener("resize", repositionPopup);
}

// Create popup element with content
function createPopup(keywordText, definition) {
    const popup = document.createElement("div");
    popup.className = "keyword-popup";
    popup.dataset.keyword = keywordText;
    popup.innerHTML = `
        <span class="popup-close"><i class="fa-solid fa-circle-xmark"></i></span>
        <div class="popup-content">
            <p>${definition.replace(/\n/g, "<br>")}</p>
        </div>
    `;
    return popup;
}

function repositionPopup() {
    const popup = document.querySelector(".keyword-popup");
    if (!popup || !activeKeyword) return;

    const rect = activeKeyword.getBoundingClientRect();
    const popupWidth = popup.offsetWidth;
    const windowWidth = window.innerWidth;

    // Define 768px centered content container
    const contentMaxWidth = 768;
    const contentLeft = (windowWidth - contentMaxWidth) / 2;
    const contentRight = contentLeft + contentMaxWidth;

    // Horizontal: Center popup on keyword
    let keywordCenter = rect.left + rect.width / 2 + window.scrollX;
    let leftPosition = keywordCenter - popupWidth / 2;

    // Clamp to content container boundaries
    if (leftPosition < contentLeft) {
        leftPosition = contentLeft + 8; // small padding from edge
    } else if (leftPosition + popupWidth > contentRight) {
        leftPosition = contentRight - popupWidth - 8;
    }

    // Vertical: position just below keyword
    let topPosition = rect.bottom + window.scrollY + 8;

    // Apply final position
    popup.style.left = `${leftPosition}px`;
    popup.style.top = `${topPosition}px`;
}
