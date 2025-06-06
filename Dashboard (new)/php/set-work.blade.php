<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Tasks • Lambda</title>
    @include("$prefix.dashboard-child.layout.stylesheet")

    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/inputs.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/layout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/header.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/sidebar.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/components.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/searchbar.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/modal.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/lmd-datepicker.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip-widgets.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/practice.css') }}?v={{ time() }}">
</head>

<body>
    <div class="wrapper">
        @include("$prefix.dashboard-child.layout.sidebar")
        <div class="main">
            <div class="border-header-page">
                <div class="container-custom">
                    @include("$prefix.dashboard-child.layout.navbar")
                </div>
            </div>

            <div class="portal-layout">
                <div class="portal-main portal-main--fullwidth">
                    <!-- Set a task -->
                    <div class="portal-section">
                        <h2 class="portal-section-title">Set a task</h2>
                        <p class="portal-section-text">
                            After you set a task, it will appear in your Home page for easy access and tracking.
                            <br>You can review completed tasks in the Review page.
                        </p>
                    </div>

                    <!-- Practice type selector -->
                    <div class="practice-type-selector" style="margin-bottom: 16px;">
                        <h2 class="portal-section-title">Select a practice type</h2>
                        <div class="practice-tab-buttons" id="pills-tab-setwork">
                            <button
                                class="tab-button active"
                                id="custom-practice-tab"
                                data-bs-target="#custom-practice"
                                type="button"
                                role="tab"
                                aria-controls="custom-practice"
                                aria-selected="true"
                            >
                                <h5>Custom practice</h5>
                                <p>Choose topics you want to improve and get practising!</p>
                            </button>
                            <button
                                class="tab-button"
                                id="mock-test-tab"
                                data-bs-target="#mock-test"
                                type="button"
                                role="tab"
                                aria-controls="mock-test"
                                aria-selected="false"
                            >
                                <h5>Mock test</h5>
                                <p>Simulate a real test to build confidence and exam skills.</p>
                            </button>
                        </div>
                    </div>

                    <!-- Tab content -->
                    <div class="tab-content" id="pills-tabContent-setwork">
                        <!-- Tab 1: Custom Practice -->
                        <div class="tab-pane active" id="custom-practice" role="tabpanel" aria-labelledby="custom-practice-tab">
                            <form id="form_submit" method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- Step 1: Choose Subject -->
                                <div class="setwork-section">
                                    <h3 class="setwork-section-title">Step 1: Choose a subject to practise</h3>
                                    <div class="show_subject"></div> <!-- Will be populated by JS -->
                                </div>

                                <!-- Step 2: Choose Topics -->
                                <div class="setwork-section">
                                    <h3 class="setwork-section-title">Step 2: Choose practice topics</h3>
                                    <div class="show_data"></div>
                                </div>

                                <!-- Step 3: Add Details -->
                                <div class="setwork-section">
                                    <h3 class="setwork-section-title">Step 3: Add practice details (Optional)</h3>
                                    <div class="setwork-input-row">
                                        <div class="setwork-input-group">
                                            <label for="practice_name" class="setwork-label">Title</label>
                                            <input
                                                type="text"
                                                id="practice_name"
                                                name="practice_name"
                                                class="lmd-input"
                                                placeholder="Enter a name"
                                            />
                                        </div>
                                        <div class="setwork-input-group">
                                            <label for="due_date" class="setwork-label">Due date</label>
                                            <div class="datepicker-wrapper">
                                                <input
                                                    type="text"
                                                    id="due_date"
                                                    name="due_date"
                                                    class="lmd-input lambda-datepicker"
                                                    placeholder="Select a date (optional)"
                                                    readonly
                                                />
                                                <div class="lambda-calendar" hidden></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setwork-slider-row">
                                        <div class="setwork-input-group">
                                            <label for="range-time" class="setwork-label">Time limit</label>
                                            <input
                                                type="range"
                                                class="lmd-slider"
                                                id="range-time"
                                                name="time_limit"
                                                min="0"
                                                max="90"
                                                step="5"
                                                value="0"
                                            />
                                            <p id="value-range-time" class="setwork-slider-output">No time limit</p>
                                        </div>
                                        <div class="setwork-input-group">
                                            <label for="range-question" class="setwork-label">Number of questions</label>
                                            <input
                                                type="range"
                                                class="lmd-slider"
                                                id="range-question"
                                                name="number_question"
                                                min="5"
                                                max="60"
                                                step="5"
                                                value="5"
                                            />
                                            <p id="value-range-question" class="setwork-slider-output">5 questions</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="setwork-toggle-row">
                                    <label class="setwork-label" for="count_score_toggle">
                                        Include this practice in performance tracking
                                    </label>
                                    <div class="toggle-placeholder">
                                        <input type="checkbox" id="count_score_toggle" name="count_score" disabled />
                                        <span class="toggle-note">(Coming soon)</span>
                                    </div>
                                </div>

                                <div class="setwork-submit-row">
                                    <button type="button" onclick="submit_pratice();" class="portal-btn portal-btn--primary">
                                        <i class="fa-solid fa-plus"></i>
                                        <span style="margin-left: 6px;">Set Task</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab 2: Mock Test -->
                        <div class="tab-pane fade" id="mock-test" role="tabpanel" aria-labelledby="mock-test-tab" tabindex="0">
                            <div class="setwork-section">
                                <h3 class="setwork-section-title">Mock Test Library</h3>
                                <!-- Search bar -->
                                <div class="setwork-searchbar">
                                    <i class="fa fa-search search-icon"></i>
                                    <input
                                        type="text"
                                        id="searchmocktest"
                                        name="searchmocktest"
                                        class="lmd-input"
                                        placeholder="Search mock tests"
                                        oninput="searchMockTest(this.value)"
                                    />
                                </div>
                                <!-- Mock test category tabs -->
                                <ul id="show_topic_01" class="setwork-filter-tabs" role="tablist">
                                    @if (@$mocktests)
                                        @foreach (@$mocktests as $key => $item)
                                            <li class="nav-item" role="presentation">
                                                <button
                                                    onclick="top_nav({{ $item->id }});"
                                                    class="nav-link link-under @if ($key == 0) active @endif"
                                                    id="mock-tab-{{ $item->id }}"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#mock-tab-pane-{{ $item->id }}"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="mock-tab-pane-{{ $item->id }}"
                                                    aria-selected="{{ $key == 0 ? 'true' : 'false' }}"
                                                >
                                                    {{ $item->name }}
                                                </button>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <!-- Hidden input to track selected topic -->
                                <input
                                    type="hidden"
                                    id="mocktests_topic_id"
                                    name="mocktests_topic_id"
                                    value="{{ @$mocktests[0]->id }}"
                                >
                                <!-- Area where cards are injected via AJAX -->
                                <div class="show_item_topic" id="show_item_topic">
                                    <!-- Populated by top_nav() or searchMockTest() -->
                                </div>
                            </div>
                        </div>

                        <!-- Overlay -->
                        <div class="portal-modal-overlay" id="modal-overlay"></div>

                        <!-- Modal Container -->
                        <div class="portal-modal" id="mocktest-modal">
                            <div class="portal-modal-content">
                                <!-- Modal Body -->
                                <div class="portal-modal-body">
                                    <h2 class="portal-modal-title" id="modal-test-title">Mock Test Title</h2>
                                    <p class="portal-modal-meta" id="modal-test-meta">50 questions • 30 minutes • Eng</p>
                                    <p class="portal-modal-description">
                                        A full-length mock test designed to review key concepts. You can take this test anytime after it’s added to your task list.
                                    </p>
                                    <p class="portal-modal-info">
                                        This mock test will be added to your task list. You can take it anytime.
                                    </p>
                                    <!-- Due Date Selector -->
                                    <div class="portal-modal-due">
                                        <label for="due_date_modal" class="setwork-label">Due date</label>
                                        <div class="datepicker-wrapper">
                                            <input
                                                type="text"
                                                id="due_date_modal"
                                                name="due_date"
                                                class="lmd-input lambda-datepicker"
                                                placeholder="Select a date (optional)"
                                                readonly
                                            />
                                            <div class="lambda-calendar" hidden></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Actions -->
                                <div class="portal-modal-actions">
                                    <button class="btn-portal-exit" onclick="closeModal()">Cancel</button>
                                    <button class="btn-portal-primary" onclick="addMockTestToTaskList()">Add to Task List</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include("$prefix.dashboard-child.layout.javascript")
    <script src="{{ asset('assets_dashboard/js/setwork-tree.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets_dashboard/js/lmd-datepicker.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets_dashboard/js/modal-handler.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets_dashboard/js/bs-switches.js') }}"></script>
    <!--Tab Switching-->
    <script>
            //Tab Switching
            document.addEventListener("DOMContentLoaded", function () {
            const tabButtons = document.querySelectorAll("#pills-tab-setwork .tab-button");
            const tabContents = document.querySelectorAll("#pills-tabContent-setwork .tab-pane");
        
            tabButtons.forEach((button) => {
                button.addEventListener("click", function () {
                // Step 1: Remove active state from all buttons and tab panels
                tabButtons.forEach((btn) => btn.classList.remove("active"));
                tabContents.forEach((panel) => panel.classList.remove("show", "active"));
        
                // Step 2: Add active class to clicked button
                this.classList.add("active");
        
                // Step 3: Find target panel and activate it
                const targetId = this.getAttribute("data-bs-target");
                const targetPanel = document.querySelector(targetId);
                if (targetPanel) {
                    targetPanel.classList.add("show", "active");
                }
                });
            });
            });
    </script>

    <!--Input Sliders-->
    <script>

        function updateSliderBg(slider) {
            const min = Number(slider.min) || 0;
            const max = Number(slider.max) || 100;
            const val = Number(slider.value);
            const percent = ((val - min) / (max - min)) * 100;
            slider.style.background = `linear-gradient(to right, #375ce3 0%, #375ce3 ${percent}%, #e5e5e5 ${percent}%, #e5e5e5 100%)`;
        }

        document.querySelectorAll('.lmd-slider').forEach(slider => {
            updateSliderBg(slider); // Initial fill
            slider.addEventListener('input', function() {
            updateSliderBg(this);
            });
        });
        
    </script>
    
    <script>
        const events = ['mousemove', 'touchmove'];
        $(document).ready(function() {
            top_nav({{ @$mocktests[0]->id }});
            selectChild();

            $(document).on('click', '.subject-radio', function () {
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

        function top_nav(mocktest_topic_id) {
            $('#mocktests_topic_id').val(mocktest_topic_id);
            $.ajax({
                type: 'GET',
                url: "dashboard-child/mocktest-subject-get",
                data: {
                    "_token": "{{ csrf_token() }}",
                    mocktest_topic_id: mocktest_topic_id,
                },
                dataType: 'html',
                success: function(data) {
                    $('#searchmocktest').val(null);
                    $('.show_item_topic').html(data);
                }
            });
        }

        $.each(events, function(k, v) {
            $('#range-time').on(v, function() {
                $('#value-range-time').text(`Time limit ${$(this).val()} mins`);
            });
            $('#range-question').on(v, function() {
                $('#value-range-question').text(`${$(this).val()} Question`);
            });
        });

        jQuery(".bt-switch").bootstrapSwitch();

        function submit_pratice() {
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
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "dashboard-child/create-pratice",
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
                                    location.href = "{{ url('dashboard-child') }}";
                                });
                            } else if (data.status == 500 || data.status == 501) {
                                Swal.fire({
                                    icon: data.status == 500 ? 'error' : 'warning',
                                    title: data.message,
                                    text: data.desc,
                                    confirmButtonText: 'Close',
                                });
                                if (data.status == 501) {
                                    $('#show_duplicate').attr('hidden', false);
                                }
                            }
                        }
                    });
                }
            });

            return false;
        }

        function selectChild() {
            var practice_child_id = {{ Auth::guard('child')->id() }};
            $.ajax({
                type: 'GET',
                url: "dashboard-child/check-subject",
                data: { practice_child_id },
                dataType: 'json',
                success: function(data) {
                    $('.show_subject').html(data.data);
                    $('input.subject-radio:checked').each(function () {
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

        function searchMockTest(query) {
            var mocktests_topic_id = $('#mocktests_topic_id').val();
            if (query === "") {
                top_nav(mocktests_topic_id);
            } else {
                $.ajax({
                    type: 'GET',
                    url: "dashboard-child/searchKeywordMocktest",
                    data: {
                        keyword: query,
                        mocktests_topic_id,
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === 'empty') {
                            $('.show_item_topic').html(null);
                        } else if (data.status === 'success') {
                            $('.show_item_topic').html(data.view);
                        }
                    }
                });
            }
        }

        function start_test(key) {
            var formData = new FormData($("#form_submit_start_test_" + key)[0]);
            $.ajax({
                type: 'POST',
                url: "dashboard-child/startmocktest",
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
                            location.reload();
                        });
                    } else if (data.status == 500 || data.status == 501) {
                        Swal.fire({
                            icon: data.status == 500 ? 'error' : 'warning',
                            title: data.message,
                            text: data.desc,
                            confirmButtonText: 'Close',
                        });
                        if (data.status == 501) {
                            $('#show_duplicate').attr('hidden', false);
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>