// mathliveKeyboard.js - Updated with new custom layout
export function setupMathliveKeyboard(mathfieldEl, options = {}) {
    if (!mathfieldEl) return null;

    if (mathfieldEl._keyboardSetup) {
        return mathfieldEl._keyboardCleanup;
    }
    mathfieldEl._keyboardSetup = true;

    // Set keyboard policy
    mathfieldEl.mathVirtualKeyboardPolicy = 'manual';

    const focusInHandler = () => {
        // Your NEW custom keyboard layout (from vanilla script)
        window.mathVirtualKeyboard.layouts = {
            label: "Lambda Basic",
            layers: [
                {
                    style: `
                        div.minimalist-backdrop {
                            display: flex;
                            justify-content: center;
                        }
                        div.minimalist-container {
                            background: #f5f5f5 !important;
                            padding: 18px;
                            border-radius: 12px;
                            box-shadow: 0 0 32px rgb(0 0 0 / 30%);
                            width: 540px;
                            margin-bottom: 84px;
                        }
                        div.minimalist-container .action {
                            min-height: 50px;
                            background: #e5e5e5 !important;
                        }
                        div.minimalist-container .action:hover {
                            background: #ececec !important;
                        }
                        div.minimalist-container .action:active {
                            background: #dcdcdc !important;
                        }
                        div.minimalist-container .lambda-done-btn {
                            background: transparent;
                            font-size: 18px;
                            font-family: rooney-sans;
                            color: #141414 !important;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        div.minimalist-container .lambda-yo-btn {
                            height: 100%;
                        }
                        div.minimalist-container .lambda-done-btn:hover {
                            color: #666666 !important;
                        }
                        div.minimalist-container .lambda-done-btn:active {
                            color: #999999 !important;
                        }
                    `,
                    backdrop: 'minimalist-backdrop',
                    container: 'minimalist-container',
                    rows: [
                        [
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            {label: '[separator]', class: 'lambda-yo-btn' },
                            { label: 'Close', command: 'noop', class: 'lambda-done-btn' }
                        ],
                        ['[hr]'],
                        [
                            '+',
                            '-',
                            '\\times',
                            '\\div',
                            { latex: '\\frac{#@}{#?}', class: 'small' },
                            '=',
                            '[.]',
                            '(',
                            ')',
                            { latex: '#@^{#?}', class: 'small' },
                        ],
                        ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
                        ['[hr]'],
                        [
                            '[undo]',
                            '[redo]',
                            '[separator]',
                            '[separator]',
                            '[separator]',
                            '[separator]',
                            {label: '[separator]', width: 0.5 },
                            '[left]',
                            '[right]',
                            { label: '[backspace]', class: 'action hide-shift' },
                        ]
                    ]
                }
            ]
        };

        window.mathVirtualKeyboard.visible = true;
        window.mathVirtualKeyboard.show();

        // Setup the Done button click handler after keyboard is shown
        setTimeout(() => {
            const doneBtn = document.querySelector('.lambda-done-btn');
            if (doneBtn) {
                doneBtn.onclick = () => {
                    console.log('[Done] .onclick triggered');
                    mathfieldEl.blur();
                };
            }
        }, 0);

        const keyboardEl = document.querySelector('.ML__keyboard');
        if (keyboardEl) {
            keyboardEl.addEventListener('touchstart', (event) => {
                event.preventDefault();
                if (document.activeElement !== mathfieldEl) {
                    mathfieldEl.focus();
                }
            }, { passive: false });
        }

        // Call custom callback if provided
        if (options.onFocus) {
            options.onFocus(mathfieldEl);
        }
    };

    const focusOutHandler = () => {
        window.mathVirtualKeyboard.hide();
        
        // Call custom callback if provided
        if (options.onBlur) {
            options.onBlur(mathfieldEl);
        }
    };

    const keyboardToggleHandler = () => {
        if (!window.mathVirtualKeyboard.visible) {
            mathfieldEl.blur();
        }
    };

        const clickHandler = (event) => {
            const keyboardEl = document.querySelector('.ML__keyboard');
            const doneBtn = event.target.closest('.lambda-done-btn');
            
            if (doneBtn) {
                console.log('[Done] global click handler triggered');
                window.mathVirtualKeyboard.hide();
                mathfieldEl.blur();
                return;
            }
            if (!mathfieldEl.contains(event.target) && !(keyboardEl && keyboardEl.contains(event.target))) {
                console.log('[Global] Click outside mathfield/keyboard');
                window.mathVirtualKeyboard.hide();
                mathfieldEl.blur();
            }
        };

    // Add event listeners
    mathfieldEl.addEventListener('focusin', focusInHandler);
    mathfieldEl.addEventListener('focusout', focusOutHandler);
    window.mathVirtualKeyboard.addEventListener('virtual-keyboard-toggle', keyboardToggleHandler);
    document.addEventListener('click', clickHandler);

    // Return cleanup function for Vue component
    return () => {
        mathfieldEl.removeEventListener('focusin', focusInHandler);
        mathfieldEl.removeEventListener('focusout', focusOutHandler);
        window.mathVirtualKeyboard.removeEventListener('virtual-keyboard-toggle', keyboardToggleHandler);
        document.removeEventListener('click', clickHandler);
    };
}

// Function to setup all math fields on the page
export function setupAllMathFields(options = {}) {
    const mathFields = document.querySelectorAll('math-field');
    const cleanupFunctions = [];
    
    mathFields.forEach(mathField => {
        const cleanup = setupMathliveKeyboard(mathField, options);
        if (cleanup) {
            cleanupFunctions.push(cleanup);
        }
    });

    // Return cleanup function for all math fields
    return () => {
        cleanupFunctions.forEach(cleanup => cleanup());
    };
}

// Auto-setup function for new math fields with cleanup
export function initMathLiveWatcher(options = {}) {
    const cleanupFunctions = [];
    
    // Use MutationObserver to watch for new math-field elements
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Element node
                    if (node.tagName === 'MATH-FIELD') {
                        const cleanup = setupMathliveKeyboard(node, options);
                        if (cleanup) {
                            cleanupFunctions.push(cleanup);
                        }
                    }
                    // Also check children
                    const mathFields = node.querySelectorAll?.('math-field');
                    mathFields?.forEach(mathField => {
                        const cleanup = setupMathliveKeyboard(mathField, options);
                        if (cleanup) {
                            cleanupFunctions.push(cleanup);
                        }
                    });
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Return cleanup function that stops observer and cleans up all listeners
    return () => {
        observer.disconnect();
        cleanupFunctions.forEach(cleanup => cleanup());
    };
}