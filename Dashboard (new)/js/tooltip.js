 document.addEventListener("DOMContentLoaded", function () {
    let currentTrigger = null; // <-- track which trigger is active

    function closeAllTooltips() {
      document.querySelectorAll('.portal-tooltip').forEach(t => {
        t.style.display = 'none';
      });
      document.querySelectorAll('[data-tooltip-id]').forEach(el => {
        el.classList.remove('active');
      });
      currentTrigger = null;
    }

    function toggleTooltip(id, triggerEl) {
      const tooltip = document.getElementById(id);
      const isVisible = tooltip && tooltip.style.display === 'block';

      if (isVisible) {
        closeAllTooltips();
        return;
      }

      closeAllTooltips(); // first close others

      if (tooltip) {
        tooltip.style.display = 'block';
        triggerEl.classList.add('active');
        currentTrigger = triggerEl;
      }
    }

    document.querySelectorAll('[data-tooltip-id]').forEach(trigger => {
      const tooltipId = trigger.getAttribute('data-tooltip-id');
      const tooltip = document.getElementById(tooltipId);

      if (tooltip) {
        trigger.addEventListener('click', function (event) {
          event.stopPropagation();
          toggleTooltip(tooltipId, trigger);
        });
      }
    });

    document.addEventListener('click', function (e) {
      // Only close if click is outside both tooltip AND trigger
      const clickedInsideTooltip = e.target.closest('.portal-tooltip');
      const clickedTrigger = e.target.closest('[data-tooltip-id]');

      if (!clickedInsideTooltip && !clickedTrigger) {
        closeAllTooltips();
      }
    });

    // Optional: expose globally
    window.toggleTooltip = toggleTooltip;
  });