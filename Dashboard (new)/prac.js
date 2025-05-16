$(document).ready(function () {
  // Show only first level
  $('.tree > ul').show();
  $('.tree > ul ul').hide();

  // Set initial icon states
  $('.tree-toggle-btn i').each(function () {
    const $li = $(this).closest('li');
    const $nestedList = $li.children('ul');
    $(this).toggleClass('fa-minus', $nestedList.is(':visible'))
           .toggleClass('fa-plus', !$nestedList.is(':visible'));
  });

  // Expand/Collapse toggle
  $('.tree-toggle-btn').on('click', function () {
    const $icon = $(this).find('i');
    const $li = $(this).closest('li');
    const $nestedList = $li.children('ul');
    
    // Toggle icon immediately with animation
    $icon.toggleClass('fa-plus fa-minus');
    
    $nestedList.slideToggle(400);
  });
 
  // Checkbox change handler
  $('.tree input[type="checkbox"]').on('change', function () {
    const $checkbox = $(this);

    // Propagate to children
    const $childCheckboxes = $checkbox.closest('li').find('ul input[type="checkbox"]');
    $childCheckboxes
      .prop('checked', $checkbox.prop('checked'))
      .prop('indeterminate', false);

    // Update all ancestors
    updateAncestors($checkbox);
  });

  function updateAncestors($checkbox) {
    const $li = $checkbox.closest('li');
    const $parentLi = $li.parents('li').first();
    const $parentCheckbox = $parentLi.children('div').find('input[type="checkbox"]');
  
    if (!$parentCheckbox.length) return;
  
    const $siblingLis = $parentCheckbox.closest('li').find('> ul > li');
    let checkedCount = 0;
    let indeterminateFound = false;
  
    $siblingLis.each(function () {
      const $cb = $(this).children('div').find('input[type="checkbox"]');
      if ($cb.prop('indeterminate')) {
        indeterminateFound = true;
      }
      if ($cb.prop('checked')) {
        checkedCount++;
      }
    });
  
    if (checkedCount === $siblingLis.length && !indeterminateFound) {
      $parentCheckbox.prop({ checked: true, indeterminate: false });
    } else if (checkedCount === 0 && !indeterminateFound) {
      $parentCheckbox.prop({ checked: false, indeterminate: false });
    } else {
      $parentCheckbox.prop({ checked: false, indeterminate: true });
    }
  
    updateAncestors($parentCheckbox);
  }
  
});
