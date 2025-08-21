// Wait for the entire HTML document to be loaded and parsed
document.addEventListener('DOMContentLoaded', () => {
    
    // Find the main container for the lesson cards
    const lessonContainer = document.querySelector('.lesson-card-container');
    if (!lessonContainer) return; // Exit if we're not on the right page

    // This script assumes that 'window.openModal' is defined by another script (modal-handler.js)
    if (typeof window.openModal !== 'function') {
        console.error('Modal handler is not available. Make sure modal-handler.js is loaded correctly.');
        return;
    }
    
    // --- Main Event Listener for Clicks ---
    lessonContainer.addEventListener('click', (e) => {
        const card = e.target.closest('.lesson-card');
        if (!card || card.classList.contains('is-current')) {
            return;
        }

        // Check if the card is meant to trigger a modal
        if (card.dataset.modalId) {
            // Prevent the default link action for completed cards
            e.preventDefault();
            
            // Call the globally available openModal function
            window.openModal(card.dataset.modalId, card);
        }
    });
    
    // --- Keyboard Support for Locked Cards ---
    lessonContainer.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        
        const card = e.target.closest('.lesson-card.is-locked');
        if (!card || !card.dataset.modalId) return;

        e.preventDefault();
        window.openModal(card.dataset.modalId, card);
    });
});