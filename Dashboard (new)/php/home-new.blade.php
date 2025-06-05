<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home • Lambda</title>
    @include("$prefix.dashboard-child.layout.stylesheet")
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/components.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/header.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/layout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/sidebar.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip-widgets.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/home.css') }}?v={{ time() }}">
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
                    <div class="portal-main portal-main--fullwidth">

                        <!-- Welcome Banner -->
                        <div class="portal-card welcome-banner">
                            <div class="welcome-banner-content">
                                <div class="welcome-text">
                                    <h3 class="welcome-title">Welcome back, {{ $child->firstname ?? '' }} 👋</h3>
                                    <p class="welcome-subtitle">I hope you're doing great — why not set your next task and earn some gems?</p>
                                    <a href="{{ url('dashboard-child/set-work') }}" class="portal-btn portal-btn--primary" style="margin-top: 16px;">Set Task</a>
                                </div>
                                <div class="welcome-mascot">
                                    <img src="{{ asset('assets_dashboard/img/mascots/dog.svg') }}" alt="Mascot" />
                                </div>
                            </div>
                        </div>

                        <!-- Task List -->
                        <div class="task-section">
                            <div class="portal-section-header">
                                <h2 class="portal-section-title">Task</h2>
                            </div>
                            <div class="task-list">
                                @forelse($pratices as $pratice)
                                    @php
                                        $sku = $pratice->sku;
                                        $answer_practices = \App\Models\Backend\Child_practice_question_answerModel::where([
                                            'child_id' => $child->id,
                                            'child_practice_main_id' => $pratice->id,
                                            'correct' => 'W'
                                        ])->first();

                                        if ($answer_practices) {
                                            $question = \App\Models\Backend\Child_practice_questionModel::find($answer_practices->child_practice_question_id);
                                            if ($question) {
                                                $sku = $question->sku;
                                            }
                                        }
                                    @endphp
                                    <a href="{{ url("dashboard-child/practice/$pratice->uuid?sku=$sku") }}" class="task-card">
                                        <div class="task-icon">
                                            @php
                                                $image = $pratice->isType != 'pratice' ? $pratice->image : optional($pratice->subject)->image;
                                            @endphp
                                            <img src="{{ \Helper::getImage($image) }}" alt="icon" />
                                        </div>
                                        <div class="task-info">
                                            <h6 class="task-title">{{ $pratice->title }}</h6>
											<p class="task-meta">
												{{ $pratice->number_question }} questions&nbsp;•&nbsp;{{ $pratice->time_limit ? $pratice->time_limit . ' minutes' : 'Untimed' }}
											</p>
                                        </div>
                                        <div class="task-hover-icon">
                                            <i class="fa-solid fa-arrow-right-long"></i>
                                        </div>
                                    </a>
                                @empty
                                    <p class="no-tasks-msg">No tasks available at the moment.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="portal-side">
                        <!-- Streak Summary -->
                        <div class="portal-card streak-summary">
                            <img class="streak-icon" src="{{ asset('assets_dashboard/img/icons/bolt.svg') }}" alt="Streak Icon">
                            <div class="streak-header">
                                <span class="streak-title">You're on a {{ $child->streak_point }} day streak.</span>
                            </div>
                            <div class="streak-footer">
                                <div class="info">
                                    Solve <strong>5</strong> problems to extend <br/> your streak.
                                </div>
                            </div>
                            <div class="streak-grid">
                                @foreach($streaks as $day)
                                    <div class="day @if($day['is_today']) is-today @endif">
                                        <div class="day-label">{{ $day['label'] }}</div>
                                        <img class="day-icon" src="{{ asset($day['icon']) }}" alt="{{ $day['status'] }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- League Card -->
                        <div class="portal-card league-card">
                            <div class="league-header">
                                <h3 class="league-title">Leaderboards</h3>
                                <span class="league-badge">Coming soon</span>
                            </div>
                            <div class="league-body">
                                <div class="league-lock-icon">
                                    <img src="{{ asset('assets_dashboard/img/icons/lock.svg') }}" alt="Locked" />
                                </div>
                                <div class="league-description">
                                    <p>Compete weekly to climb the leaderboard and earn exclusive rewards.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Summary -->
                        <div class="portal-card weekly-summary-card">
                            <div class="weekly-summary-header">
                                <h3 class="weekly-summary-title">Your Weekly Activity</h3>
                            </div>
                            <div class="weekly-summary-stats">
                                <div class="weekly-stat-item">
                                    <div class="stat-icon">
                                        <img src="{{ asset('assets_dashboard/img/icons/aim.svg') }}" alt="Correct answers" />
                                    </div>
                                    <div class="stat-info">
                                        <p class="stat-label">Correct answers</p>
                                        <p class="stat-value">42</p>
                                    </div>
                                </div>
                                <div class="weekly-stat-item">
                                    <div class="stat-icon">
                                        <img src="{{ asset('assets_dashboard/img/icons/hourglass.svg') }}" alt="Time spent" />
                                    </div>
                                    <div class="stat-info">
                                        <p class="stat-label">Time spent learning</p>
                                        <p class="stat-value">234 minutes</p>
                                    </div>
                                </div>
                                <div class="weekly-stat-item">
                                    <div class="stat-icon">
                                        <img src="{{ asset('assets_dashboard/img/icons/done.svg') }}" alt="Tasks done" />
                                    </div>
                                    <div class="stat-info">
                                        <p class="stat-label">Tasks completed</p>
                                        <p class="stat-value">33</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("$prefix.dashboard-child.layout.javascript")
	<script>
	  document.querySelectorAll('a.task-card').forEach(el => {
		el.addEventListener('touchstart', () => {});
	  });
	</script>
</body>

</html>