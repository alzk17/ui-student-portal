<aside id="sidebar" class="expand">
    <!-- Sidebar logo section -->
    <div class="logo-sidebar">
        <div class="sidebar-logo">
            <a href="{{ url("$folder") }}">
              <img src="{{ asset('assets_dashboard/img/logos/logo-big.svg') }}" class="logo-expanded" alt="">
              <img src="{{ asset('assets_dashboard/img/logos/logo-small.svg') }}" class="logo-collapsed" alt="Small Logo">
            </a>
        </div>
    </div>

    <!-- Sidebar navigation menu -->
    <ul class="sidebar-nav">
        <li class="sidebar-item {{ request()->is("$folder") ? 'active' : '' }}">
            <a href="{{ url("$folder") }}" class="sidebar-link">
                <img src="{{ asset('assets_dashboard/img/icons/home.svg') }}" class="img-fluid img-icon" alt=""> <span>Home</span>
            </a>
        </li>
        <li class="sidebar-item {{ (request()->is("$folder/journey") || request()->is("$folder/journey/*")) ? 'active' : '' }}">
            <a href="{{ url("$folder/journey") }}" class="sidebar-link">
                <img src="{{ asset('assets_dashboard/img/icons/hat.svg') }}" class="img-fluid img-icon" alt="">
                <span>Learn</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/set-work") ? 'active' : '' }}">
            <a href="{{ url("$folder/set-work") }}" class="sidebar-link">
                <img src="{{ asset('assets_dashboard/img/icons/target.svg') }}" class="img-fluid img-icon" alt=""> <span>Set Tasks</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/review") ? 'active' : '' }}">
            <a href="{{ url("$folder/review") }}" class="sidebar-link">
                <img src="{{ asset('assets_dashboard/img/icons/magnifier.svg') }}" class="img-fluid img-icon" alt="">
                <span>Review</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/worksheet") ? 'active' : '' }}">
            <a href="{{ url("$folder/worksheet") }}" class="sidebar-link">
                <img src="{{ asset('assets_dashboard/img/icons/binders.svg') }}" class="img-fluid img-icon" alt="">
                <span>Resources</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/hangouts") ? 'active' : '' }}">
            <a href="{{ url("$folder/hangouts") }}" class="sidebar-link">
                <img src="{{ asset('assets_dashboard/img/icons/shop.svg') }}" class="img-fluid img-icon-step2" alt="">
                <span>Shop</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/profile") ? 'active' : '' }}">
            <a href="{{ url("$folder/profile") }}" class="sidebar-link">
                <img src="{{ asset('assets_dashboard/img/icons/id.svg') }}" class="img-fluid img-icon-step2" alt="">
                <span>Profile</span>
            </a>
        </li>
    </ul>

    <!-- Sidebar footer with dropdown menu -->
    <div class="sidebar-footer">
        <div class="dropdown">
            <a class="dropdown-toggle sidebar-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('assets_dashboard/img/icons/more-test.svg') }}" class="img-fluid img-icon" alt=""> <span>More</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ url("$folder/settings") }}"><span>Settings</span></a></li>
                <li><a class="dropdown-item" href="{{ url("$folder/help") }}"><span>Help</span></a></li>
                <li><a class="dropdown-item" href="#" onclick="openProblemReportModal();"><span>Report a problem</span></a></li>
                <li><div class="dropdown-divider"></div></li>
                <li><a class="dropdown-item" href="{{ url('dashboard-parent') }}"><span>Switch to parent account</span></a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"><span>Log out</span></a></li>
            </ul>
        </div>
    </div>
</aside>