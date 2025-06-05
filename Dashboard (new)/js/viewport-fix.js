  // Fix for iOS Safari and other touch devices viewport height issues
function setViewportHeight() {
  // Get the actual viewport height
  let vh = window.innerHeight * 0.01;
  // Set the custom property to the root
  document.documentElement.style.setProperty('--vh', `${vh}px`);
}

// Set on initial load
setViewportHeight();

// Re-calculate on resize and orientation change
window.addEventListener('resize', setViewportHeight);
window.addEventListener('orientationchange', () => {
  // Small delay to ensure the viewport has settled after orientation change
  setTimeout(setViewportHeight, 100);
});

// For iOS Safari - handle viewport changes during scrolling
let ticking = false;
function handleScroll() {
  if (!ticking) {
    requestAnimationFrame(() => {
      setViewportHeight();
      ticking = false;
    });
    ticking = true;
  }
}

// Only add scroll listener on touch devices
if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
  window.addEventListener('scroll', handleScroll, { passive: true });
}