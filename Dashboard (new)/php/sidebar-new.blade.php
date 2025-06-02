<aside id="sidebar" class="expand">
    <!-- Sidebar logo section -->
    <div class="logo-sidebar">
        <div class="sidebar-logo">
            <a href="{{ url("$folder") }}">
                <img src="{{ asset('Icons/test-logo-big.svg') }}" class="logo-expanded" alt="">
                <img src="{{ asset('Icons/test-logo-small.svg') }}" class="logo-collapsed" alt="Small Logo">
            </a>
        </div>
    </div>

    <!-- Sidebar navigation menu -->
    <ul class="sidebar-nav">
        <li class="sidebar-item {{ request()->is("$folder") ? 'active' : '' }}">
            <a href="{{ url("$folder") }}" class="sidebar-link">
                <img src="{{ asset('Icons/home-2.svg') }}" class="img-fluid img-icon" alt=""> <span>Home</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/journey") ? 'active' : '' }}">
            <a href="{{ url("$folder/journey") }}" class="sidebar-link">
                <img src="{{ asset('Icons/hat-1.svg') }}" class="img-fluid img-icon" alt="">
                <span>Learn</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/set-work") ? 'active' : '' }}">
            <a href="{{ url("$folder/set-work") }}" class="sidebar-link">
                <img src="{{ asset('Icons/target-2.svg') }}" class="img-fluid img-icon" alt=""> <span>Set Tasks</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/review") ? 'active' : '' }}">
            <a href="{{ url("$folder/review") }}" class="sidebar-link">
                <img src="{{ asset('Icons/magnifier-2.svg') }}" class="img-fluid img-icon" alt="">
                <span>Review</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/worksheet") ? 'active' : '' }}">
            <a href="{{ url("$folder/worksheet") }}" class="sidebar-link">
                <img src="{{ asset('Icons/binders-5.svg') }}" class="img-fluid img-icon" alt="">
                <span>Resources</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/hangouts") ? 'active' : '' }}">
            <a href="{{ url("$folder/hangouts") }}" class="sidebar-link">
                <img src="{{ asset('Icons/shop-1.svg') }}" class="img-fluid img-icon-step2" alt="">
                <span>Shop</span>
            </a>
        </li>
        <li class="sidebar-item {{ request()->is("$folder/profile") ? 'active' : '' }}">
            <a href="{{ url("$folder/profile") }}" class="sidebar-link">
                <img src="{{ asset('Icons/id-card-2.svg') }}" class="img-fluid img-icon-step2" alt="">
                <span>Profile</span>
            </a>
        </li>
    </ul>

    <!-- Sidebar footer with dropdown menu -->
    <div class="sidebar-footer">
        <div class="dropdown">
            <a class="dropdown-toggle sidebar-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('Icons/more-test.svg') }}" class="img-fluid img-icon" alt=""> <span>More</span>
            </a>

            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"><span>Settings</span></a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"><span>Help</span></a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"><span>Report a problem</span></a></li>
                <li><div class="dropdown-divider"></div></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"><span>Switch to parent account</span></a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"><span>Log out</span></a></li>
            </ul>
        </div>
    </div>
</aside>