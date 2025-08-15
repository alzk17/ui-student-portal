<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Learn • Lambda</title>
  @include("$prefix.dashboard-child.layout.stylesheet")

  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/components.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/header.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/layout.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/sidebar.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip-widgets.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/learn.css') }}?v={{ time() }}">
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

                      <!-- LEARNING JOURNEY ROW-->
                      <section class="portal-section">
                        <h2 class="portal-section-title learn-title">Learning Journeys</h2>
                        <p class="portal-section-text learn-subtext">
                            Follow bite-sized lessons, track your progress, and master each topic step‑by‑step.
                        </p>

                        <div class="course-list">
                            @forelse ($courses as $i => $course)
                            @php
                                $sectionId = 'course-'.Str::slug($course['title'] ?? 'journey-'.$i);
                                $topics = $course['topics'] ?? [];
                            @endphp

                            <section class="course-section" id="{{ $sectionId }}" role="region" aria-labelledby="{{ $sectionId }}-title">
                                <div class="course-section__heading">
                                <h3 class="course-section__title" id="{{ $sectionId }}-title">
                                    {{ $course['title'] ?? 'Learning Journey' }}
                                </h3>
                                </div>

                                <ul class="topic-cards" aria-label="Topics in {{ $course['title'] ?? 'this journey' }}">
                                @forelse ($topics as $topic)
                                    <li class="topic-card">
                                    <a class="topic-card__link" href="{{ $topic['url'] ?? '#' }}" aria-label="Open {{ $topic['title'] ?? 'topic' }}">
                                        @php $icon = $topic['icon'] ?? null; @endphp
                                        @if($icon)
                                          <img
                                            class="topic-card__icon"
                                            src="{{ Str::startsWith($icon, ['http://','https://','//']) ? $icon : asset($icon) }}"
                                            alt=""
                                            loading="lazy"
                                            decoding="async"
                                          >
                                        @endif
                                        <h4 class="topic-card__title">{{ $topic['title'] ?? 'Untitled Topic' }}</h4>
                                    </a>
                                    </li>
                                @empty
                                    <li class="topic-card topic-card--empty" aria-hidden="true">
                                    <div class="topic-card__link" tabindex="-1">
                                        <h4 class="topic-card__title">No topics yet</h4>
                                    </div>
                                    </li>
                                @endforelse
                                </ul>
                            </section>

                            <div class="course-section__divider" role="presentation"></div>
                            @empty
                            <div class="learn-jrny-card" style="width:100%;justify-content:center;">
                                <div style="padding:24px 0;text-align:center;color:#bbb;">Coming soon!</div>
                            </div>
                            @endforelse
                        </div>
                      </section>

                  </div>
              </div>
          </div>
      </div>
  </div>
  @include("$prefix.dashboard-child.layout.javascript")
</body>

</html>