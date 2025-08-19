<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>hello brother</title>
  @include("$prefix.dashboard-child.layout.stylesheet")

  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/components.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/header.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/layout.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/sidebar.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/modal.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip-widgets.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/lesson-map.css') }}?v={{ time() }}">
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
                        
                        {{-- Section 1: Topic Overview --}}
                        <section class="portal-section course-overview">
                            <div class="course-details">
                                <div class="topic-breadcrumb">
                                    {{-- Use the 'journey' relationship to get the parent course name --}}
                                    <a href="{{ url('dashboard-child/journey') }}">{{ $subject->journey->name ?? 'Course' }}</a>
                                    <i class="fa-solid fa-chevron-right"></i>
                                    <span>{{ $subject->name ?? 'Topic' }}</span>
                                </div>
                                <h1 class="course-title">{{ $subject->name ?? 'Topic Title' }}</h1>
                                <p class="course-description">
                                    {{ $subject->description ?? 'Learn the basics of this topic step-by-step.' }}
                                </p>
                                <button type="button" class="portal-btn3d portal-btn3d--blue portal-btn3d--primary btn-start-learning">
                                  Continue learning
                                </button>
                            </div>

                            <div class="course-dashboard">
                                <div class="progress-card">
                                    <div class="progress-header">
                                        <span class="progress-label">Topic Progress</span>
                                        <span class="progress-percent">{{-- $progressPercent --}}%</span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: {{-- $progressPercent --}}%;"></div>
                                    </div>
                                </div>

                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <div class="stat-label">Lessons</div>
                                        <div class="stat-value">{{-- $lessonCount --}}</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-label">Practices</div>
                                        <div class="stat-value">{{-- $practiceCount --}}</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-label">Level</div>
                                        <div class="stat-value">{{ $subject->level ?? 'Beginner' }}</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-label">Est. Time</div>
                                        <div class="stat-value">{{ $subject->estimated_time ?? '2 hours' }}</div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        
                        {{-- Section 2: Syllabus / Lesson Cards --}}
                        <section class="portal-section syllabus-map">
                            <div class="lesson-card-container">

                                @forelse($lessons as $lesson)
                                    @php
                                        // This is where you would add your logic to determine the lesson's status
                                        // For example:
                                        // $isCompleted = in_array($lesson->id, $completedLessonIds);
                                        // $isCurrent = ($lesson->id == $currentLessonId);
                                        // $isLocked = (!$isCompleted && !$isCurrent);
                                        
                                        // For this template, we'll just use a placeholder status
                                        $status = 'locked';
                                        if ($loop->iteration < 3) $status = 'completed';
                                        if ($loop->iteration == 3) $status = 'current';
                                    @endphp

                                    @if($status === 'locked')
                                        <div class="lesson-card is-locked" aria-label="Locked: {{ $lesson->name }}">
                                    @else
                                        <a class="lesson-card is-{{$status}} card-link" href="{{ url('dashboard-child/journey/' . $journey->id . '/' . $subject->id . '/learning?lesson=' . $lesson->id) }}">
                                    @endif

                                        <div class="card-index" aria-hidden="true">{{ $loop->iteration }}</div>
                                        <div class="card-content">
                                            <div class="card-tags">
                                                {{-- Assuming your lesson object has a 'type' property --}}
                                                <span class="tag tag--type">{{ ucfirst($lesson->type) }}</span>
                                            </div>
                                            <h3 class="card-title">{{ $lesson->name }}</h3>
                                            <p class="card-meta">
                                                @if($status === 'completed')
                                                    You've completed this.
                                                @elseif($status === 'current')
                                                    This is your next step.
                                                @else
                                                    Complete the previous step to unlock.
                                                @endif
                                            </p>
                                        </div>
                                        <div class="card-state-icon" aria-hidden="true">
                                            @if($status === 'completed')
                                                <i class="fa-solid fa-circle-check"></i>
                                            @elseif($status === 'current')
                                                <i class="fa-solid fa-play"></i>
                                            @else
                                                <i class="fa-solid fa-lock"></i>
                                            @endif
                                        </div>

                                    @if($status === 'locked')
                                        </div>
                                    @else
                                        </a>
                                    @endif
                                @empty
                                    <p style="text-align: center; color: var(--grey-600);">No lessons have been added to this topic yet.</p>
                                @endforelse

                            </div>
                        </section>
                    </div>
                </div>
            </div>

            {{-- Modals can go here if needed --}}

        </div>
    </div>
    @include("$prefix.dashboard-child.layout.javascript")
    <script>
		var fullUrl = window.location.href;
		var pathname = window.location.pathname.replace('/','');
		var journeyId = pathname.split('/')[3];
		var subjectId = pathname.split('/')[4];
		var userId = document.querySelector('input[name="userId"]').value;
		var progressBar = document.querySelector('.skill-progress');
		const completeIcon = document.createElement('i');
		completeIcon.setAttribute('class','fa-solid fa-check');
		const playIcon = document.createElement('i');
		playIcon.setAttribute('class','fa-solid fa-play');
		const practiceIcon = document.createElement('i');
		practiceIcon.setAttribute('class','fa-solid fa-edit');
		let practices = 0;
		let lessons = 0;
		
		const tabs = document.createElement('div');
		tabs.id = "canvas-TabContent";
		tabs.classList.add('tab-content');
		
		const getLessons = async () => {
			const request = await fetch(`${fullUrl}/get/lessons`);
			if(!request.ok) console.log(request.statusText);
			const response = await request.json();
			fetchLessonsData(response);
		}
		getLessons();
        const fetchLessonsData = (data) => {
            console.log('All data >>> ', data);

            const posts = document.getElementById('posts');
            const contElement = document.querySelector('.count');

            // This will hold the count of each type
            let lessonCount = 0;
            let practiceCount = 0;

            // Loop through all lessons provided by the API
            data.lessons.map((v, k) => {
                // Create a new entry element for the map
                let entryEl = document.createElement('div');
                entryEl.setAttribute('class', 'entry');
                entryEl.dataset.id = v.id;
                entryEl.dataset.type = v.type; // Get the type ('digest' or 'application')

                // Construct the correct URL with the lesson ID as a query parameter
                const lessonUrl = `${fullUrl}/learning?lesson=${v.id}`;

                // Use the correct icon based on the lesson type
                const iconClass = v.type === 'application' ? 'fa-solid fa-edit' : 'fa-solid fa-play';

                // Set the HTML for the lesson node
                entryEl.innerHTML = `
                    <div class="entry-timeline cursor-pointer">
                        <i class="${iconClass}" style="font-size: 16px !important;"></i>
                        <div class="timeline-divider"></div>
                    </div>
                    <div class="entry-title title-sm p-3">
                        <a href="${lessonUrl}" class="learn-this">
                            <p class="fw-light">${v.name}</p>
                        </a>
                    </div>`;

                // Add the new node to the map
                posts.append(entryEl);

                // Increment the count for the lesson/practice display
                if (v.type === 'application') {
                    practiceCount++;
                } else {
                    lessonCount++;
                }
            });

            // Update the lesson and practice counts on the page
            contElement.querySelector('.lesson').innerHTML = lessonCount;
            contElement.querySelector('.practice').innerHTML = practiceCount;

            // We can re-implement the logic for showing which nodes are 'active' or 'complete' later.
            // For now, this focuses on building the map correctly.
        };
		// const progress = () => {
		// }

		document.addEventListener('click',function(e){
			const backBtn = e.target.closest('#back');
			if(backBtn){
				window.location.replace('/dashboard-child/journey');
			}
			const resetBtn = e.target.closest('#reset');
			if(resetBtn) {
				if(confirm('Confirm learning reset') == true){ 
					let req = learningReset();
					req.then(res => {
						if(res.status != 200){
							alert(`${res.status} ${res.statusText}`)
						}else{
							location.reload();
						}
					})
				}
			}
		})
		const  learningReset = async() => {
			const request = await fetch(`${window.location.href}/reset`);
			if(!request.ok){
				return request;
			}
			const response = await request.json();
			return response;
		}
	</script>
</body>

</html>