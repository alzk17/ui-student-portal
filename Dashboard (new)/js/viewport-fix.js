function isiOS() {
  return (
    /iPad|iPhone|iPod/.test(navigator.userAgent) ||
    (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)
  );
}

if (isiOS()) {
  document.body.classList.add('ios-fix');
} else {
  // Only apply viewport hack for NON-iOS devices!
  function setViewportHeight() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  }

  setViewportHeight();
  window.addEventListener('resize', setViewportHeight);
  window.addEventListener('orientationchange', () => {
    setTimeout(setViewportHeight, 100);
  });

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
  if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
    window.addEventListener('scroll', handleScroll, { passive: true });
  }
}
