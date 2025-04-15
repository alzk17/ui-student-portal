$(document).ready(function() {
    // Event Listeners for Range Sliders
    const events = ['mousemove', 'touchmove'];
    $.each(events, function(k, v) {
        $('#range-time').on(v, function() {
            const value = $('#range-time').val();
            $('#value-range-time').text(value == 0 ? 'No time limit' : `${value} minutes`);
        });

        $('#range-question').on(v, function() {
            const value = $('#range-question').val();
            $('#value-range-question').text(`${value} questions`);
        });
    });

    jQuery(".bt-switch").bootstrapSwitch();

    // Set initial icon state
    $('.toggle-btn').each(function() {
        var $icon = $(this).find('i');
        var $nestedList = $(this).closest('li').children('ul');

        if ($nestedList.is(':visible')) {
            $icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            $icon.removeClass('fa-minus').addClass('fa-plus');
        }
    });

    // Expand/Collapse Functionality
    $('.toggle-btn').on('click', function() {
        var $this = $(this);
        var $icon = $this.find('i'); // FontAwesome icon
        var $nestedList = $this.closest('li').children('ul'); // Get child list

        if ($nestedList.is(':visible')) {
            $nestedList.slideUp(400); // Collapse smoothly
            $icon.removeClass('fa-minus').addClass('fa-plus'); // Change icon
        } else {
            $nestedList.slideDown(400); // Expand smoothly
            $icon.removeClass('fa-plus').addClass('fa-minus'); // Change icon
        }
    });

    // Checkbox Parent-Child Logic
    $('.tree input[type="checkbox"]').on('change', function() {
        var $checkbox = $(this);
        var $childCheckboxes = $checkbox.closest('li').find('ul input[type="checkbox"]');
        var $parentCheckbox = $checkbox.closest('ul').siblings('input[type="checkbox"]');

        // If checked, check all children
        if ($checkbox.prop('checked')) {
            $childCheckboxes.prop('checked', true).prop('indeterminate', false);
        } else {
            $childCheckboxes.prop('checked', false).prop('indeterminate', false);
        }

        updateParentCheckbox($parentCheckbox);
    });

    // Function to Handle Indeterminate Parent State
    function updateParentCheckbox($parentCheckbox) {
        if ($parentCheckbox.length) {
            var $childCheckboxes = $parentCheckbox.siblings('ul').find('input[type="checkbox"]');
            var checkedChildren = $childCheckboxes.filter(':checked').length;
            var totalChildren = $childCheckboxes.length;

            if (checkedChildren === totalChildren) {
                $parentCheckbox.prop('checked', true).prop('indeterminate', false); // Fully checked
            } else if (checkedChildren > 0) {
                $parentCheckbox.prop('checked', false).prop('indeterminate', true); // Partially checked
            } else {
                $parentCheckbox.prop('checked', false).prop('indeterminate', false); // Fully unchecked
            }

            updateParentCheckbox($parentCheckbox.closest('ul').siblings('input[type="checkbox"]'));
        }
    }

    // Practice Submission Function
    function submit_practice() {
        var formData = new FormData($("#form_submit")[0]);
        var level_id = $('#level_id').val();
        var practice_name = $('#practice_name').val();

        if (practice_name == "" || level_id == "") {
            toastr.error("Sorry, please complete the information.");
            return false;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Please press confirm to complete the transaction.',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: `Cancel`,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: "dashboard-child/create-practice",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: data.message,
                                text: data.desc,
                                confirmButtonText: 'Close',
                            }).then(() => {
                                location.href = "https://lambda.in.th/dashboard-child";
                            });
                        } else {
                            Swal.fire({
                                icon: data.status == 500 ? 'error' : 'warning',
                                title: data.message,
                                text: data.desc,
                                confirmButtonText: 'Close',
                            });
                        }

                        if (data.status == 501) {
                            Swal.close();
                            $('#show_duplicate').attr('hidden', false);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        return false;
    }

    // Subject Selection Logic
    function selectChild() {
        var practice_child_id = 16;
        $.ajax({
            type: 'GET',
            url: "dashboard-child/check-subject",
            data: { practice_child_id: practice_child_id },
            dataType: 'json',
            success: function(data) {
                $('.show_subject').html(data.data);
                $('input.subject-radio:checked').each(function() {
                    select_radio.call(this);
                });
            }
        });
    }

    function select_radio() {
        var selectedSubjectId = $(this).data('subject-id');
        $.ajax({
            type: 'GET',
            url: "get/subject/practice",
            data: { subject_id: selectedSubjectId },
            dataType: 'html',
            success: function(data) {
                $('.show_data').html(data);
            }
        });
    }

    // Initialize Subject Selection
    selectChild();

    // Handle Subject Radio Click
    $(document).on('click', '.subject-radio', function() {
        var selectedSubjectId = $(this).data('subject-id');
        $.ajax({
            type: 'GET',
            url: "get/subject/practice",
            data: { subject_id: selectedSubjectId },
            dataType: 'html',
            success: function(data) {
                $('.show_data').html(data);
            }
        });
    });
});
