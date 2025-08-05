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
                            <h2 class="portal-section-title">Learning Journeys</h2>
                            <div class="learn-jrny-row">
                                @if(isset($journeys) && count($journeys) > 0)
                                    @foreach($journeys as $journey)
                                    <a href="{{ url('dashboard-child/journey/' . $journey->journey_id . '/' . $journey->id) }}" class="learn-jrny-card" style="text-decoration:none;color:inherit;">
                                        <div class="learn-jrny-content">
                                            <div class="learn-jrny-label">Learning Journey</div>
                                            <div class="learn-jrny-title">{{ $journey->name }}</div>
                                            <div class="learn-jrny-meta">
                                                <span class="learn-jrny-count">0 topics</span>
                                            </div>
                                        </div>
                                        <div class="learn-jrny-image">
                                            <img src="{{ asset($journey->image) }}" alt="{{ $journey->name }} Icon">
                                        </div>
                                    </a>
                                    @endforeach
                                @else
                                    <div class="learn-jrny-card" style="width:100%;justify-content:center;">
                                        <div style="padding:24px 0;text-align:center;color:#bbb;">Coming soon!</div>
                                    </div>
                                @endif
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