<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review • Lambda</title>
  
  @include("$prefix.dashboard-child.layout.stylesheet")
  
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/header.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/layout.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/components.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/searchbar.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/inputs.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/modal.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/sidebar.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip-widgets.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/review.css') }}?v={{ time() }}">
  <script>
    MathJax = {
      options: {
        enableMenu: false // Disables the MathJax context menu
      },
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']]
      }
    };
  </script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
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

        <div class="container-custom">
          <div class="portal-layout">
            <main class="portal-main portal-main--fullwidth">
              
              <section class="portal-section">
                <h2 class="portal-section-title">Review and Reflect</h2>
                <p class="portal-section-text">
                  This page shows all the tasks you've completed or are still working on. You can check your performance, view scorecards, or continue any unfinished tasks.
                </p>
              </section>

              <section class="portal-section">
                <h2 class="portal-section-title">Your Tasks</h2>
                
                <!-- FILTER ROW: Place here, before the table! -->
                <div class="task-filter-row">
                  <div class="lmd-toggle-switch" data-toggle-name="tasktype">
                    <button type="button" class="toggle-option {{ request('type') != 'mocktest' ? 'active' : '' }}" data-value="pratice"
                        onclick="window.location.href='{{ url('dashboard-child/review?type=pratice') }}'">
                        Practice
                    </button>
                    <button type="button" class="toggle-option {{ request('type') == 'mocktest' ? 'active' : '' }}" data-value="mock"
                        onclick="window.location.href='{{ url('dashboard-child/review?type=mocktest') }}'">
                        Mock Test
                    </button>
                  </div>
                  <div class="review-searchbar">
                    <i class="fa fa-search search-icon"></i>
                    <input
                        type="text"
                        id="searchreview"
                        name="searchreview"
                        class="lmd-input"
                        placeholder="Search tasks"
                    >
                  </div>
                </div>
                <!-- END FILTER ROW -->

                <div class="task-table box-table table-responsive">
                    <table class="table box-table">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="title" data-type="text">Title <i class="fas fa-sort" style="display:none;"></i></th>
                                <th class="sortable" data-sort="subject" data-type="text">Subject <i class="fas fa-sort" style="display:none;"></i></th>
                                <th class="sortable" data-sort="score" data-type="score">Score <i class="fas fa-sort" style="display:none;"></i></th>
                                <th class="sortable" data-sort="due-date" data-type="date">Due <i class="fas fa-sort" style="display:none;"></i></th>
                                <th class="sortable" data-sort="completed-date" data-type="date">Completed <i class="fas fa-sort" style="display:none;"></i></th>
                                <th class="sortable" data-sort="status" data-type="status">Status <i class="fas fa-sort" style="display:none;"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pratices as $item)
                                <tr>
                                    <td>{{ $item->title ?? '-' }}</td>
                                    <td>{{ $item->subject->name ?? '-' }}</td>
                                    <td>
                                        @if($item->isActive == 'S' && $item->correct_answer !== null && $item->total_question > 0)
                                            {{ round(($item->correct_answer / $item->total_question) * 100) . '%' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>-</td> <!-- No Due date yet, so always '-' -->
                                    <td>
                                        @if($item->isActive == 'S' && $item->complete_date)
                                            {{ \Carbon\Carbon::parse($item->complete_date)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->isActive == 'W')
                                            <a class="status-table" href="{{ url("$folder/practice/$item->uuid") }}">
                                                <span class="text-success">Start</span>
                                            </a>
                                        @elseif($item->isActive == 'I')
                                            <a class="status-table" href="{{ url("$folder/practice/$item->uuid") }}">
                                                <span class="text-success">Continue</span>
                                            </a>
                                        @elseif($item->isActive == 'S')
                                            <a class="status-table"
                                              href="javascript:void(0);"
                                              onclick="openReviewModal('{{ $item->uuid }}')">
                                              <span class="text-success">Review</span>
                                            </a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center;">No data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <nav class="lmd-pagination-wrapper" aria-label="Page navigation">
                  <ul class="pagination lmd-pagination" id="pagination"></ul>
                </nav>
              </section>

            </main>
          </div>
        </div>

        </div>
  </div>

  @include("$prefix.dashboard-child.review-scorecard-modal")
  @include("$prefix.dashboard-child.layout.javascript")

  <script src="{{ asset('assets_dashboard/js/modal-handler.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('assets_dashboard/js/review-table.js') }}?v={{ time() }}"></script>
  <script>
    function openReviewModal(uuid) {
      // Show overlay and modal
      document.getElementById('modal-overlay').classList.add('active');
      document.getElementById('scorecard-modal').classList.add('active');
      document.body.setAttribute('data-current-modal', 'scorecard-modal');
      
      // Optionally: Clear any previous content
      $('#scorecard-modal-title').text('Loading...');
      $('#scorecard-summary-row').html('');
      $('#scorecard-answers-list').html('<div style="padding:1rem;text-align:center;">Loading...</div>');

      // Fetch data
      $.ajax({
        url: '/dashboard-child/review/scorecard/' + uuid,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
          if (!res || !res.practice) {
            $('#scorecard-modal-title').text('Error loading data');
            $('#scorecard-answers-list').html('<div style="color:#b71c1c;">No data found.</div>');
            return;
          }
          // Populate modal
          fillScorecardModal(res);
        },
        error: function() {
          $('#scorecard-modal-title').text('Error loading data');
          $('#scorecard-answers-list').html('<div style="color:#b71c1c;">Failed to load data.</div>');
        }
      });
    }

    function renderAnswerSection(q) {
      let html = `<div class="scorecard-answer-row">`;
      if (q.user_answer !== undefined && q.user_answer !== null && q.user_answer !== "") {
        html += `<span class="scorecard-user-answer-label">Your answer:</span> <span class="scorecard-user-answer">${q.user_answer}</span>`;
      } else {
        html += `<span class="scorecard-user-answer-label">No answer given.</span>`;
      }
      html += `</div>`;
      return html;
    }

    function fillScorecardModal(res) {
      // 1. Title
      $('#scorecard-modal-title').text((res.practice.title || 'Practice') + ' - Results');

      // 2. Summary row
      $('#scorecard-summary-row').html(`
        <div class="scorecard-summary-card">
          <img src="{{ asset('assets_dashboard/img/icons/aim.svg') }}" class="scorecard-summary-icon" alt="">
          <div>
            <div class="scorecard-summary-title">Correct questions</div>
            <div class="scorecard-summary-value">${res.practice.correct_answer}/${res.practice.total_question}</div>
          </div>
        </div>
        <div class="scorecard-summary-card">
          <img src="{{ asset('assets_dashboard/img/icons/hourglass.svg') }}" class="scorecard-summary-icon" alt="">
          <div>
            <div class="scorecard-summary-title">Total time spent</div>
            <div class="scorecard-summary-value">${res.practice.time_spent || '-'}</div>
          </div>
        </div>
        <div class="scorecard-summary-card">
          <img src="{{ asset('assets_dashboard/img/icons/gem.svg') }}" class="scorecard-summary-icon" alt="">
          <div>
            <div class="scorecard-summary-title">Gems earned</div>
            <div class="scorecard-summary-value">${res.practice.gems || '0'}</div>
          </div>
        </div>
      `);

      // 3. Questions
      let answerHtml = '';
      (res.questions || []).forEach((q, idx) => {
        answerHtml += `
          <div class="scorecard-accordion-item ${q.is_correct ? 'correct' : 'incorrect'}">
            <button class="scorecard-accordion-header" onclick="toggleScorecardAccordion(this)">
              <div class="scorecard-question-meta">
                <div class="scorecard-question-label-row">
                  <i class="fa-solid fa-${q.is_correct ? 'check' : 'xmark'} icon-feedback"></i>
                  <span class="scorecard-question-label">Question ${idx + 1}</span>
                </div>
                <span class="scorecard-question-time">
                  <i class="fa-solid fa-clock"></i> &nbsp;${q.time_spent || '-'}
                </span>
              </div>
              <span class="scorecard-accordion-chevron">
                <i class="fa-solid fa-chevron-down"></i>
              </span>
            </button>
            <div class="scorecard-accordion-body">
              <div class="scorecard-question-text">
                <p>${q.text}</p>
              </div>
              ${renderAnswerSection(q)}
              <div class="scorecard-explanation-toggle-row">
                <button class="scorecard-explanation-toggle" onclick="toggleScorecardExplanation(this)">
                  Answer Explanation
                  <span class="scorecard-explanation-chevron">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </button>
              </div>
              <div class="scorecard-explanation-body">
                <p>${q.explanation || ''}</p>
              </div>
            </div>
          </div>
        `;
      });
      $('#scorecard-answers-list').html(answerHtml);

      $('#scorecard-answers-list').html(answerHtml);

      // Tell MathJax to render any new math inside the modal!
      if (window.MathJax && window.MathJax.typesetPromise) {
        MathJax.typesetPromise([document.getElementById('scorecard-modal')]);
      }
    }
  </script>

  <script>
    function toggleScorecardAccordion(btn) {
      const item = btn.closest('.scorecard-accordion-item');
      item.classList.toggle('open');
    }

    function toggleScorecardExplanation(btn) {
      const body = btn.closest('.scorecard-accordion-body').querySelector('.scorecard-explanation-body');
      btn.classList.toggle('open');
      body.classList.toggle('open');
    }
  </script>

</body>
</html>