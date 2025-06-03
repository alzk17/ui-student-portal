        <div class="box-headerpage">

            {{-- Profile Section (Left Side) --}}
            <div class="search-bar-wrapper">
                <div class="profile-mini" data-tooltip-id="profile-tooltip">
                    <div class="box-image">
                        <img src="{{ asset($child->image ?? 'assets_dashboard/img/mascots/elephant.svg') }}" class="img-fluid" alt="Profile">
                    </div>
                    <div class="profile-name">
                        {{ $child->firstname ?? '' }}
                    </div>

                    <div class="portal-tooltip portal-tooltip--profile" id="profile-tooltip">
                        <div class="tooltip-profile-header">
                            <img src="{{ asset($child->image ?? 'assets_dashboard/img/mascots/elephant.svg') }}" alt="Profile" class="tooltip-profile-image" />
                            <div class="tooltip-profile-info">
                                <p class="tooltip-title">{{ $child->fullname() }}</p>
                                <div class="tooltip-info-pair">
                                    <span class="tooltip-label">Age:</span>
                                    <span class="tooltip-value">{{ $child->age ?? '-' }}</span>
                                </div>
                                <div class="tooltip-info-pair">
                                    <span class="tooltip-label">Plan:</span>
                                    <span class="tooltip-value">{{ $child->plan_name ?? '-' }}</span>
                                </div>
                                <p class="tooltip-value">You have {{ $child->days_remaining ?? 0 }} days remaining.</p>
                            </div>
                        </div>
                        <div class="tooltip-cta-wrapper">
                            <a href="/top-up" class="portal-btn portal-btn--primary" style="width: 100%;">Top up now</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side Icons --}}
            <div class="right-icons-wrapper">

                {{-- Streak --}}
                <div class="box-streak">
                    <div><img src="{{ asset('assets_dashboard/img/icons/bolt.svg') }}" alt="Streak Icon" /></div>
                    <div class="streak-counter">{{ $child->streak_point ?? 0 }}</div>
                </div>

                {{-- Gems --}}
                <div class="box-gem" data-tooltip-id="gem-tooltip">
                    <div>
                        <img src="{{ asset('assets_dashboard/img/icons/gem.svg') }}" class="img-fluid" alt="Gem Icon">
                    </div>
                    <div class="gem-counter">{{ \App\Helpers\Helper::formatCompactNumber($child->wallet_point ?? 0) }}</div>

                    {{-- Gem Tooltip --}}
                    <div class="portal-tooltip portal-tooltip--gem" id="gem-tooltip">
                        <div class="tooltip-gem-content">
                            <div class="tooltip-gem-icon">
                                <img src="{{ asset('assets_dashboard/img/icons/gem.svg') }}" alt="Gem Icon" />
                            </div>
                            <div class="tooltip-gem-text">
                                <p class="gem-count"><span style="color: #375ce3">{{ number_format($child->wallet_point ?? 0) }}</span> Gems</p>
                                <p class="tooltip-info" style="margin: 0;">
                                    Use them to buy streak shields or items at the
                                    <a href="/dashboard-child/hangouts" class="tooltip-link">Shop</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notification --}}
                <div class="box-notification has-unread" data-tooltip-id="notification-tooltip">
                    <img src="{{ asset('assets_dashboard/img/icons/bell.svg') }}" alt="Notification Icon" />
                </div>

            </div>

        </div>