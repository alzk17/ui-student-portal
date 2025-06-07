function enableToggleSwitch(selector, callback) {
  document.querySelectorAll(selector).forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      if (e.target.classList.contains('toggle-option')) {
        // Remove 'active' from all options
        toggle.querySelectorAll('.toggle-option').forEach(btn => btn.classList.remove('active'));
        // Add 'active' to clicked
        e.target.classList.add('active');
        // Run callback if provided
        if (typeof callback === 'function') {
          callback(e.target.dataset.value, toggle.dataset.toggleName, e.target);
        }
      }
    });
  });
}

// Usage Example:
enableToggleSwitch('.lmd-toggle-switch', function(selectedValue, groupName, buttonEl) {
  // Do something with the selected value
  // Example: filter table, update UI, etc.
  console.log('Toggle group:', groupName, 'Value:', selectedValue);
});
