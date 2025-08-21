<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subject->name ?? 'Topic Title' }} • Lambda</title>
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
                                </div>
                                <h1 class="course-title">{{ $subject->name ?? 'Topic Title' }}</h1>
                                <p class="course-description">
                                    {{ $subject->detail ?? 'Learn the basics of this topic step-by-step.' }}
                                </p>
                                {{-- Smart "Continue Learning" button with state-aware logic (safe fallbacks) --}}
                                @if($topicProgressPercent == 100)
                                    {{-- SCENARIO 1: All lessons are complete --}}
                                    <a href="{{ url('dashboard-child/journey') }}" class="portal-btn3d portal-btn3d--blue portal-btn3d--primary btn-start-learning">
                                        Back to Courses
                                    </a>
                                @elseif($topicProgressPercent > 0)
                                    {{-- SCENARIO 2: The topic is in progress --}}
                                    @if(!empty($continueUrl))
                                        <a href="{{ $continueUrl }}" class="portal-btn3d portal-btn3d--blue portal-btn3d--primary btn-start-learning">
                                            Continue learning
                                        </a>
                                    @else
                                        <a href="{{ url('dashboard-child/journey') }}" class="portal-btn3d portal-btn3d--blue portal-btn3d--primary btn-start-learning">
                                            Back to Courses
                                        </a>
                                    @endif
                                @else
                                    {{-- SCENARIO 3: The topic has not been started --}}
                                    @if(!empty($continueUrl))
                                        <a href="{{ $continueUrl }}" class="portal-btn3d portal-btn3d--blue portal-btn3d--primary btn-start-learning">
                                            Start learning
                                        </a>
                                    @else
                                        <a href="{{ url('dashboard-child/journey') }}" class="portal-btn3d portal-btn3d--blue portal-btn3d--primary btn-start-learning">
                                            Back to Courses
                                        </a>
                                    @endif
                                @endif
                            </div>

                            <div class="course-dashboard">
                                <div class="progress-card">
                                    <div class="progress-header">
                                        <span class="progress-label">Topic Progress</span>
                                    </div>
                                    <div class="progress-row">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width: {{ $topicProgressPercent ?? 0 }}%;">
                                                <span class="progress-bar-percent">{{ $topicProgressPercent ?? 0 }}%</span>
                                            </div>
                                        </div>
                                        <button class="btn-reset-progress" title="Reset topic progress" aria-label="Reset topic progress">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <div class="stat-label">Lessons</div>
                                        <div class="stat-value">{{ $lessons->where('type', '!=', 'application')->count() }}</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-label">Practices</div>
                                        <div class="stat-value">{{ $lessons->where('type', 'application')->count() }}</div>
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
                                        // FINAL LOGIC: Determine status based on real data
                                        // IMPORTANT: Give "current" precedence over "completed" so the next lesson always shows as current
                                        $isCompleted = in_array($lesson->id, $completedLessonIds ?? []);
                                        $isCurrent = ($lesson->id == $currentLessonId);

                                        if ($isCurrent) {
                                            $status = 'current';
                                        } elseif ($isCompleted) {
                                            $status = 'completed';
                                        } else {
                                            $status = 'locked';
                                        }
                                    @endphp

                                    @if($status === 'locked')
                                        <div class="lesson-card is-locked" aria-label="Locked: {{ $lesson->name }}" role="button" tabindex="0"
                                             data-modal-id="lesson-locked-modal"
                                             data-title="{{ $lesson->name }}"
                                             data-start-url="{{ url('dashboard-child/journey/' . $journey->id . '/' . $subject->id . '/learning?lesson=' . $lesson->id) }}"
                                             data-current-url="{{ $continueUrl ?? url('dashboard-child/journey/' . $journey->id . '/' . $subject->id . '/learning?lesson=' . ($currentLessonId ?? '')) }}">
                                    @else
                                        <a class="lesson-card is-{{$status}} card-link"
                                           href="@if($status === 'completed')#@else{{ url('dashboard-child/journey/' . $journey->id . '/' . $subject->id . '/learning?lesson=' . $lesson->id) }}@endif"
                                           @if($status === 'completed')
                                             role="button" tabindex="0"
                                             aria-label="Review: {{ $lesson->name }} (completed)"
                                             data-modal-id="lesson-completed-modal"
                                             data-title="{{ $lesson->name }}"
                                             data-review-url="{{ url('dashboard-child/journey/' . $journey->id . '/' . $subject->id . '/learning?lesson=' . $lesson->id) }}"
                                             data-restart-url="{{ url('dashboard-child/journey/' . $journey->id . '/' . $subject->id . '/reset') }}"
                                           @endif
                                        >
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
            <!-- Modal Overlay (shared) -->
            <div id="modal-overlay" class="portal-modal-overlay" hidden></div>

            <!-- Completed Lesson Modal -->
            <div id="lesson-completed-modal"
                 class="portal-modal lesson-modal lesson-modal--completed"
                 role="dialog" aria-modal="true" aria-labelledby="completed-title" hidden>
              <div class="portal-modal-content">
                <h2 id="completed-title" class="portal-modal-title">Lesson Complete!</h2>
                <p class="portal-modal-description">
                  You’ve already finished this lesson. Would you like to review or start fresh?
                </p>
                <div class="portal-modal-actions">
                  <button type="button" class="btn-portal-primary" data-action="review">Review lesson</button>
                  <button type="button" class="btn-portal-exit" data-action="reset">Reset progress</button>
                </div>
              </div>
            </div>

            <!-- Locked Lesson Modal -->
            <div id="lesson-locked-modal"
                 class="portal-modal lesson-modal lesson-modal--locked"
                 role="dialog" aria-modal="true" aria-labelledby="locked-title" hidden>
              <div class="portal-modal-content">
                <h2 id="locked-title" class="portal-modal-title">Jump to this lesson?</h2>
                <p class="portal-modal-description">
                  Earlier lessons stay locked. 100% requires finishing all lessons.
                </p>
                <div class="portal-modal-actions">
                  <button type="button" class="btn-portal-primary" data-action="jump">Jump anyway</button>
                  <button type="button" class="btn-portal-exit" data-action="goto-current">Go to current</button>
                </div>
              </div>
            </div>

            <!-- Topic Reset Confirmation Modal (affects ALL lessons in this topic) -->
            <div id="topic-reset-modal"
                class="portal-modal lesson-modal lesson-modal--confirm"
                role="dialog" aria-modal="true" aria-labelledby="topic-reset-title" hidden>
                <div class="portal-modal-content">
                    <h2 id="topic-reset-title" class="portal-modal-title">Reset topic progress?</h2>
                    <p class="portal-modal-description">
                    This will clear progress for all lessons in this topic. You can’t undo this.
                    </p>
                    <div class="portal-modal-actions">
                    <button type="button" class="btn-portal-exit" data-action="cancel-reset">Cancel</button>
                    <button type="button" class="btn-portal-primary" data-action="confirm-reset">Reset</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include("$prefix.dashboard-child.layout.javascript")
    <script src="{{ asset('custom/js/modal-handler.js') }}?v={{ time() }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- RESET BUTTON MODAL INTEGRATION ---
            const resetButton = document.querySelector('.btn-reset-progress');
            const topicResetModal = document.getElementById('topic-reset-modal');
            const modalOverlay = document.getElementById('modal-overlay');

            if (resetButton && topicResetModal) {
                // Handle reset button click - open modal
                resetButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Reset button clicked'); // Debug log
                    
                    // Show modal and overlay
                    if (modalOverlay) {
                        modalOverlay.removeAttribute('hidden');
                    }
                    topicResetModal.removeAttribute('hidden');
                    
                    // If you have a global openModal function, use it too
                    if (typeof window.openModal === 'function') {
                        window.openModal('topic-reset-modal');
                    }
                });

                // Handle modal action buttons
                const confirmButton = topicResetModal.querySelector('button[data-action="confirm-reset"]');
                const cancelButton = topicResetModal.querySelector('button[data-action="cancel-reset"]');

                if (confirmButton) {
                    confirmButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Confirm reset clicked'); // Debug log
                        
                        // Perform the reset
                        fetch("{{ url('dashboard-child/journey/' . $journey->id . '/' . $subject->id . '/reset') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 200) {
                                window.location.reload();
                            } else {
                                alert('An error occurred: ' + (data.message || 'Please try again.'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        })
                        .finally(() => {
                            closeResetModal();
                        });
                    });
                }

                if (cancelButton) {
                    cancelButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Cancel reset clicked'); // Debug log
                        closeResetModal();
                    });
                }

                // Handle clicking overlay to close modal
                if (modalOverlay) {
                    modalOverlay.addEventListener('click', function(e) {
                        if (e.target === modalOverlay) {
                            closeResetModal();
                        }
                    });
                }

                // Handle ESC key to close modal
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !topicResetModal.hasAttribute('hidden')) {
                        closeResetModal();
                    }
                });
            }

            // Helper function to close the reset modal
            function closeResetModal() {
                if (topicResetModal) {
                    topicResetModal.setAttribute('hidden', '');
                }
                if (modalOverlay) {
                    modalOverlay.setAttribute('hidden', '');
                }
                
                // If you have a global closeModal function, use it too
                if (typeof window.closeModal === 'function') {
                    window.closeModal();
                }
            }

            // --- EXISTING LESSON CARD MODAL LOGIC ---
            // Keep your existing modal logic for lesson cards
            if (typeof window.openModal === 'function') {
                document.addEventListener('click', function(e) {
                    const card = e.target.closest('.lesson-card.is-completed, .lesson-card.is-locked');
                    if (card) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const modalId = card.dataset.modalId;
                        const modal = document.getElementById(modalId);
                        
                        if (modal) {
                            modal.dataset.cardPayload = JSON.stringify(card.dataset);
                            window.openModal(modalId, card);
                        }
                    }
                });

                // Handle lesson modal actions
                document.addEventListener('click', function(e) {
                    const actionButton = e.target.closest('.portal-modal-actions button[data-action]');
                    if (!actionButton) return;
                    
                    const modal = actionButton.closest('.portal-modal');
                    
                    // Skip if this is the topic reset modal (handled above)
                    if (modal && modal.id === 'topic-reset-modal') {
                        return;
                    }
                    
                    const action = actionButton.dataset.action;
                    const data = JSON.parse(modal?.dataset.cardPayload || '{}');
                    
                    if (data) {
                        const urls = {
                            review: data.reviewUrl,
                            reset: data.restartUrl,
                            jump: data.startUrl,
                            'goto-current': data.currentUrl
                        };
                        
                        if (urls[action]) {
                            window.location.href = urls[action];
                        }
                    }
                    window.closeModal();
                });
            }
        });
    </script>
    <script>
      // Simple modal delegation - works with existing modal-handler.js
      document.addEventListener('click', function(e) {
        const completedCard = e.target.closest('.lesson-card.is-completed');
        const lockedCard = e.target.closest('.lesson-card.is-locked');
        
        if (completedCard || lockedCard) {
          e.preventDefault();
          e.stopPropagation();
          
          const card = completedCard || lockedCard;
          const modalId = card.dataset.modalId || (completedCard ? 'lesson-completed-modal' : 'lesson-locked-modal');
          
          // Store data for action buttons
          const modal = document.getElementById(modalId);
          if (modal) {
            modal._data = card.dataset;
            document.getElementById('modal-overlay')?.removeAttribute('hidden');
            modal.removeAttribute('hidden');
            openModal(modalId, card); // Use existing function
          }
        }
      }, true);

      // Handle action buttons
      document.addEventListener('click', function(e) {
        const action = e.target.dataset?.action;
        if (!action) return;
        
        const modalId = document.body.getAttribute('data-current-modal');
        const modal = document.getElementById(modalId);
        const data = modal?._data;
        
        if (data) {
          const urls = {
            review: data.reviewUrl,
            reset: data.restartUrl, 
            jump: data.startUrl,
            'goto-current': data.currentUrl
          };
          
          if (urls[action] && (action !== 'reset' || confirm('Reset progress?'))) {
            window.location.href = urls[action];
          }
        }
        closeModal();
      });
    </script>
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