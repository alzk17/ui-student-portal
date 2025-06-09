<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review • Lambda</title>
  @include("$prefix.dashboard-child.layout.stylesheet")
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/header.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/layout.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/components.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/sidebar.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/searchbar.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/inputs.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip-widgets.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/review.css') }}?v={{ time() }}">
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
                                    <td>-</td>
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
                                            <a class="status-table" href="{{ url("$folder/practice-transcript/$item->uuid") }}">
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
  @include("$prefix.dashboard-child.layout.javascript")
  <script src="{{ asset('assets_dashboard/js/modal-handler.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('assets_dashboard/js/review-table.js') }}?v={{ time() }}"></script>
</body>
</html>