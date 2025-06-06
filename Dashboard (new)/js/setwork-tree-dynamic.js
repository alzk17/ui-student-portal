// 1. Function to (re-)initialize the tree state
function initTreeState() {
  $('.tree > ul').show();
  $('.tree > ul ul').hide();
  $('.tree-toggle-btn i').each(function () {
    const $li = $(this).closest('li');
    const $nestedList = $li.children('ul');
    $(this)
      .toggleClass('fa-minus', $nestedList.is(':visible'))
      .toggleClass('fa-plus', !$nestedList.is(':visible'));
  });
}

// 2. Delegated event handlers (for all trees, even injected)
$(document).on('click', '.tree-toggle-btn', function () {
  const $icon = $(this).find('i');
  const $li = $(this).closest('li');
  const $nestedList = $li.children('ul');
  $icon.toggleClass('fa-plus fa-minus');
  $nestedList.slideToggle(400);
});

$(document).on('change', '.tree input[type="checkbox"]', function () {
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

// 3. Run only once on first page load
$(document).ready(function () {
  initTreeState();
});