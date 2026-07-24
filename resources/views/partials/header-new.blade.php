    <header class="main-header cvvv">
        <a href="/admin/" class="logo">
            <span class="logo-mini">
                @if (isset($logoPath) && !empty($logoPath) && file_exists(public_path($logoPath)))
                <img src="{{ $logoPath }}" alt="{{ $siteName ?? trans('global.site_title') }}" />
                <span> Ridon </span>
                @else
                <b>{{ $siteName ?? trans('global.site_title') }}</b>
                @endif
            </span>
            <span class="logo-lg">
                @if (isset($logoPath) && !empty($logoPath) && file_exists(public_path($logoPath)))
                <img src="{{ $logoPath }}" alt="{{ $siteName ?? trans('global.site_title') }}" />
                <span>Ridon</span>
                @else
                {{ $siteName ?? trans('global.site_title') }}
                @endif
            </span>
        </a>

        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">{{ trans('global.toggleNavigation') }}</span>
            </a>
            <div class="quick-links-container dropdown">
                <div class="quick-links-container dropdown">
                    <button class="btn btn-quick-links">
                        Quick Links <i class="fa fa-plus"></i>
                    </button>
                    <div class="dropdown-menu quick-links-menu">
                        <div class="ql-header">Quick Links</div>

                        <div class="ql-grid">
                            <a href="{{ route('admin.home') }}" class="ql-item">
                                <div class="ql-icon-circle"><i class="fa fa-dashboard"></i></div>
                                <span>{{ trans('menu.dashboard') }}</span>
                            </a>

                            @can('booking_access')
                            <a href="{{ route('admin.bookings.index') }}" class="ql-item">
                                <div class="ql-icon-circle"><i class="fa fa-calendar"></i></div>
                                <span>{{ trans('menu.booking_list') }}</span>
                            </a>
                            @endcan

                            @can('app_user_access')
                            <a href="{{ route('admin.drivers.index') }}" class="ql-item">
                                <div class="ql-icon-circle"><i class="fa fa-id-badge"></i></div>
                                <span>{{ trans('menu.drivers') }}</span>
                            </a>

                            <a href="{{ route('admin.app-users.index', ['user_type' => 'user']) }}" class="ql-item">
                                <div class="ql-icon-circle"><i class="fa fa-user"></i></div>
                                <span>{{ trans('menu.riders') }}</span>
                            </a>
                            @endcan

                            @can('coupon_access')
                            <a href="{{ route('admin.add-coupons.index') }}" class="ql-item">
                                <div class="ql-icon-circle"><i class="fa fa-ticket"></i></div>
                                <span>{{ trans('menu.coupon_title') }}</span>
                            </a>
                            @endcan

                            @can('general_setting_access')
                            <a href="{{ route('admin.settings') }}" class="ql-item">
                                <div class="ql-icon-circle"><i class="fa fa-cogs"></i></div>
                                <span>{{ trans('menu.settings') }}</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    @can('language_setting_access')
                    @if (count(config('global.available_languages', [])) > 1)
                    @endif
                    @endcan
                </ul>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="#"
                                class="icon-circle"
                                title="Clear Cache"
                                onclick="event.preventDefault(); document.getElementById('clear-cache-form').submit();">
                                <i class="fa fa-paint-brush"></i>
                            </a>

                            <form id="clear-cache-form"
                                action="{{ route('admin.clear.cache') }}"
                                method="POST"
                                style="display:none;">
                                @csrf
                            </form>
                        </li>

                        <li>
                            <a href="https://welcome-rideon.unibooker.app/" target="_blank" class="icon-circle" title="Visit Landing Page">
                                <i class="fa fa-globe"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle icon-circle text-icon">
                                EN
                            </a>
                            <ul class="dropdown-menu language-menu">
                                <li>
                                    <a href="?change_language=en">
                                        <img src="/images/icon/us.png" class="flag-icon" alt="US"> English (en)
                                    </a>
                                </li>
                                <!-- <li>
                                    <a href="?change_language=hi">
                                        <img src="/images/icon/in.png" class="flag-icon" alt="IN"> Hindi (hi)
                                    </a>
                                </li>
                                <li>
                                    <a href="?change_language=ar">
                                        <img src="/images/icon/sa.png" class="flag-icon" alt="AR"> Arabic (ar)
                                    </a>
                                </li>
                                <li>
                                    <a href="?change_language=fr">
                                        <img src="/images/icon/fr.png" class="flag-icon" alt="FR"> French (fr)
                                    </a>
                                </li>
                                <li>
                                    <a href="?change_language=es">
                                        <img src="/images/icon/es.png" class="flag-icon" alt="ES"> Spanish (es)
                                    </a>
                                </li> -->

                            </ul>
                        </li>
                       <li>
    <a href="#" class="icon-circle" title="Dark Mode" id="dark-mode-toggle">
        <i class="fa fa-moon-o"></i>
    </a>
</li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle icon-circle text-icon" data-toggle="dropdown">
                                SOS
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header" style="padding: 10px 20px; font-weight:bold; border-bottom:1px solid #eee;">
                                    Recent SOS Alerts
                                </li>
                                <li>
                                    <div class="empty-state-container">
                                        <svg viewBox="0 0 500 250" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="250" cy="125" r="100" fill="#f8f9fa" />
                                            <circle cx="420" cy="50" r="8" fill="#1D56A5" opacity="0.2" />
                                            <rect x="50" y="80" width="15" height="15" rx="3" fill="#e0e0e0" />
                                            <path d="M180 230 Q180 150 250 150 Q320 150 320 230" fill="#1D56A5" />
                                            <circle cx="250" cy="110" r="40" fill="#fbd38d" />
                                            <path d="M210 110 Q210 60 250 60 Q290 60 290 110" fill="#333" />
                                            <circle cx="235" cy="110" r="3" fill="#333" />
                                            <circle cx="265" cy="110" r="3" fill="#333" />
                                            <path d="M240 135 Q250 125 260 135" stroke="#333" stroke-width="2" fill="none" />
                                            <path d="M240 230 L360 230 L380 170 L260 170 Z" fill="#4a4a4a" />
                                            <rect x="270" y="175" width="100" height="50" rx="2" fill="#333" />
                                            <path d="M360 120 Q360 70 420 70 Q480 70 480 120 Q480 170 420 170 L400 190 L400 170 Q360 170 360 120" fill="white" stroke="#e0e0e0" stroke-width="2" />
                                            <rect x="395" y="90" width="50" height="65" rx="4" fill="#f8f9fa" stroke="#1D56A5" stroke-width="2" />
                                            <path d="M410 110 L430 110 M415 125 Q420 120 425 125" stroke="#1D56A5" stroke-width="3" stroke-linecap="round" fill="none" />
                                            <text x="400" y="145" font-family="Arial" font-size="8" font-weight="bold" fill="#1D56A5">NO DATA</text>
                                            <g class="q-marks">
                                                <text x="210" y="50" font-family="Arial" font-size="24" font-weight="bold" fill="#1D56A5">?</text>
                                                <text x="240" y="35" font-family="Arial" font-size="28" font-weight="bold" fill="#1D56A5">?</text>
                                                <text x="280" y="55" font-family="Arial" font-size="24" font-weight="bold" fill="#1D56A5">?</text>
                                            </g>
                                        </svg>
                                        <div class="empty-state-text">No SOS Alert Found</div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle icon-circle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header" style="padding: 10px 20px; font-weight:bold; border-bottom:1px solid #eee;">
                                    Notifications
                                </li>
                                <li>
                                    <div class="empty-state-container">
                                        <svg viewBox="0 0 500 250" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="250" cy="125" r="100" fill="#f8f9fa" />
                                            <circle cx="420" cy="50" r="8" fill="#1D56A5" opacity="0.2" />
                                            <rect x="50" y="80" width="15" height="15" rx="3" fill="#e0e0e0" />
                                            <path d="M180 230 Q180 150 250 150 Q320 150 320 230" fill="#1D56A5" /> <!-- Shirt -->
                                            <circle cx="250" cy="110" r="40" fill="#fbd38d" /> <!-- Face -->
                                            <path d="M210 110 Q210 60 250 60 Q290 60 290 110" fill="#333" /> <!-- Hair -->
                                            <circle cx="235" cy="110" r="3" fill="#333" />
                                            <circle cx="265" cy="110" r="3" fill="#333" />
                                            <path d="M240 135 Q250 125 260 135" stroke="#333" stroke-width="2" fill="none" />
                                            <path d="M240 230 L360 230 L380 170 L260 170 Z" fill="#4a4a4a" /> <!-- Base -->
                                            <rect x="270" y="175" width="100" height="50" rx="2" fill="#333" /> <!-- Screen Area -->
                                            <path d="M360 120 Q360 70 420 70 Q480 70 480 120 Q480 170 420 170 L400 190 L400 170 Q360 170 360 120" fill="white" stroke="#e0e0e0" stroke-width="2" />
                                            <rect x="395" y="90" width="50" height="65" rx="4" fill="#f8f9fa" stroke="#1D56A5" stroke-width="2" />
                                            <path d="M410 110 L430 110 M415 125 Q420 120 425 125" stroke="#1D56A5" stroke-width="3" stroke-linecap="round" fill="none" />
                                            <text x="400" y="145" font-family="Arial" font-size="8" font-weight="bold" fill="#1D56A5">NO DATA</text>
                                            <g class="q-marks">
                                                <text x="210" y="50" font-family="Arial" font-size="24" font-weight="bold" fill="#1D56A5">?</text>
                                                <text x="240" y="35" font-family="Arial" font-size="28" font-weight="bold" fill="#1D56A5">?</text>
                                                <text x="280" y="55" font-family="Arial" font-size="24" font-weight="bold" fill="#1D56A5">?</text>
                                            </g>
                                        </svg>
                                        <div class="empty-state-text">Coming Soon</div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle icon-circle" data-toggle="dropdown">
                                <i class="fa fa-comment-o"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header" style="padding: 10px 20px; font-weight:bold; border-bottom:1px solid #eee;">
                                    Recent Chats
                                </li>
                                <li>
                                    <div class="empty-state-container">
                                        <svg width="140" height="120" viewBox="0 0 150 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="chat-animation">
                                            <path class="bubble-1" d="M14.5 48.5C14.5 24.4756 35.7665 5 62 5C88.2335 5 109.5 24.4756 109.5 48.5C109.5 72.5244 88.2335 92 62 92C56.634 92 51.5033 91.1852 46.7329 89.6808L24.5 98V77.7288C18.4239 70.3644 14.5 60.103 14.5 48.5Z" fill="#ffca3b" />
                                            <rect x="38" y="32" width="50" height="6" rx="3" fill="white" fill-opacity="0.4" />
                                            <rect x="38" y="46" width="50" height="6" rx="3" fill="white" fill-opacity="0.4" />
                                            <rect x="38" y="60" width="30" height="6" rx="3" fill="white" fill-opacity="0.4" />
                                            <path class="bubble-2" d="M125 76.5C125 58.5507 109.106 44 89.5 44C69.8939 44 54 58.5507 54 76.5C54 94.4493 69.8939 109 89.5 109C93.5015 109 97.3323 108.384 100.893 107.253L117.5 113.5V98.2619C122.031 92.7301 125 85.0471 125 76.5Z" fill="#E6B51C" />
                                        </svg>
                                        <div class="empty-state-text">Coming Soon<</div>
                                    </div>

                                </li>
                            </ul>
                        </li>
                      @php
    $user = auth()->user();
    $user->loadMissing('roles');
    $roleTitle = $user->roles->pluck('title')->first();
@endphp
@php
    $user = auth()->user();
    $user->loadMissing('roles');
    $roleTitle = $user->roles->pluck('title')->first();

    $profileImage = $user->profile_image
        ? asset($user->profile_image)
        : asset('storage/logo/608330.714930.png');
@endphp

<li class="dropdown user">
    <a href="#" class="dropdown-toggle user-profile-link" data-toggle="dropdown">
        <img src="{{ $profileImage }}" class="user-image" alt="User Image">
        <div class="user-info-text hidden-xs">
            <span class="user-name">{{ $user->name }}</span>
            <span class="user-role">{{ ucwords($roleTitle) }}</span>
        </div>
    </a>

    <ul class="dropdown-menu user-menu-dropdown">
        <li>
            <a href="/profile/password">
                <i class="fa fa-user-o"></i> Edit Profile
            </a>
        </li>

        <li>
            <a href="#"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out"></i> Logout
            </a>

            <form id="logout-form" action="/logout" method="POST" style="display:none;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </li>
    </ul>
</li>


                    </ul>
                </div>
            </div>
        </nav>
    </header>