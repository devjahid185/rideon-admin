<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> {{ isset($siteName) && $siteName ? $siteName : trans('global.site_title') }}</title>
    <link rel="shortcut icon" href="{{ $faviconPath ?? asset('default/favicon.png') }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"
        rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/AdminLTE.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/skins/_all-skins.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
    <style>
        .top-loader {
            height: 3px;
            background-color: #7e7013;
            width: 0%;
            transition: width 0.5s ease;
        }
    </style>

    <!-- Ionicons -->
    <link rel="stylesheet" atr="a"
        href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link type="text/css" href="{{ asset('css/custom.css') }}?{{ time() }}" rel="stylesheet" />
    <link type="text/css" href="{{ asset('css/dashboard.css') }}?{{ time() }}" rel="stylesheet" />
    @yield('styles')

</head>

<body class="sidebar-mini skin-purple" style="height: auto; min-height: 100%;">
    <div x-data="pageTransition()" x-init="init()" x-cloak
        style="position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;">
        <div x-ref="bar" class="top-loader"></div>
    </div>

    <div class="wrapper" style="height: auto; min-height: 100%;">
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
                
                <!-- Container for the component -->
<div class="quick-links-container dropdown">
    
    <!-- The Button -->
    

    <!-- The Dropdown Card -->
    <div class="quick-links-container dropdown">
    
    <!-- Button -->
    <button class="btn btn-quick-links">
        Quick Links <i class="fa fa-plus"></i>
    </button>

    <!-- Dropdown Menu -->
    <div class="dropdown-menu quick-links-menu">
        <div class="ql-header">Quick Links</div>
        
        <div class="ql-grid">
            <!-- 1 -->
            <a href="#" class="ql-item">
                <div class="ql-icon-circle"><i class="fa fa-car"></i></div>
                <span>Create Ride</span>
            </a>
            <!-- 2 -->
            <a href="#" class="ql-item">
                <div class="ql-icon-circle"><i class="fa fa-map-marker"></i></div>
                <span>Driver Location</span>
            </a>
            <!-- 3 -->
            <a href="#" class="ql-item">
                <div class="ql-icon-circle"><i class="fa fa-user-plus"></i></div>
                <span>Add Driver</span>
            </a>
            <!-- 4 -->
            <a href="#" class="ql-item">
                <div class="ql-icon-circle"><i class="fa fa-map-o"></i></div>
                <span>Add Zone</span>
            </a>
            <!-- 5 -->
            <a href="#" class="ql-item">
                <div class="ql-icon-circle"><i class="fa fa-ticket"></i></div>
                <span>Coupon</span>
            </a>
            <!-- 6 -->
            <a href="#" class="ql-item">
                <div class="ql-icon-circle"><i class="fa fa-file-text-o"></i></div>
                <span>Reports</span>
            </a>
        </div>
    </div>
</div>
</div>

                <div class="navbar-custom-menu" >
                    <ul class="nav navbar-nav">

                        @can('language_setting_access')
                            @if (count(config('global.available_languages', [])) > 1)
                                
                            @endif
                        @endcan
                    </ul>
                    
                    <div class="navbar-custom-menu" >
            <ul class="nav navbar-nav">

                <!-- 1. Theme Icon -->
                <li>
                    <a href="#" class="icon-circle" title="Theme">
                        <i class="fa fa-paint-brush"></i>
                    </a>
                </li>

                <!-- 2. Website Icon -->
                <li>
                    <a href="/" target="_blank" class="icon-circle" title="Visit Website">
                        <i class="fa fa-globe"></i>
                    </a>
                </li>

                <!-- 3. Language Selector -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle icon-circle text-icon" >
                        EN
                    </a>
                    <ul class="dropdown-menu language-menu">
                        <li>
                            <a href="?change_language=en">
                                <img src="/images/icon/us.png" class="flag-icon" alt="US"> English (en)
                            </a>
                        </li>
                        <li>
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
                        </li>
                       
                    </ul>
                </li>

            <!-- 4. Dark Mode -->
<li>
    <a href="#" class="icon-circle" title="Dark Mode">
        <i class="fa fa-moon-o"></i>
    </a>
</li>

<!-- 5. SOS Alerts -->
<!-- REMOVED 'notifications-menu' to prevent JS conflict -->
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
            <!-- Background Decoration -->
            <circle cx="250" cy="125" r="100" fill="#f8f9fa" />
            <circle cx="420" cy="50" r="8" fill="#1D56A5" opacity="0.2" />
            <rect x="50" y="80" width="15" height="15" rx="3" fill="#e0e0e0" />

            <!-- The Person -->
            <path d="M180 230 Q180 150 250 150 Q320 150 320 230" fill="#1D56A5" /> <!-- Shirt -->
            <circle cx="250" cy="110" r="40" fill="#fbd38d" /> <!-- Face -->
            <path d="M210 110 Q210 60 250 60 Q290 60 290 110" fill="#333" /> <!-- Hair -->
            
            <!-- Eyes & Mouth (Sad) -->
            <circle cx="235" cy="110" r="3" fill="#333" />
            <circle cx="265" cy="110" r="3" fill="#333" />
            <path d="M240 135 Q250 125 260 135" stroke="#333" stroke-width="2" fill="none" />

            <!-- Laptop -->
            <path d="M240 230 L360 230 L380 170 L260 170 Z" fill="#4a4a4a" /> <!-- Base -->
            <rect x="270" y="175" width="100" height="50" rx="2" fill="#333" /> <!-- Screen Area -->

            <!-- Thought Bubble -->
            <path d="M360 120 Q360 70 420 70 Q480 70 480 120 Q480 170 420 170 L400 190 L400 170 Q360 170 360 120" fill="white" stroke="#e0e0e0" stroke-width="2" />
            
            <!-- No Data Document -->
            <rect x="395" y="90" width="50" height="65" rx="4" fill="#f8f9fa" stroke="#1D56A5" stroke-width="2" />
            <path d="M410 110 L430 110 M415 125 Q420 120 425 125" stroke="#1D56A5" stroke-width="3" stroke-linecap="round" fill="none" />
            <text x="400" y="145" font-family="Arial" font-size="8" font-weight="bold" fill="#1D56A5">NO DATA</text>

            <!-- Animated Question Marks -->
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

<!-- 6. Notifications -->
<!-- REMOVED 'notifications-menu' class -->
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
            <!-- Background Decoration -->
            <circle cx="250" cy="125" r="100" fill="#f8f9fa" />
            <circle cx="420" cy="50" r="8" fill="#1D56A5" opacity="0.2" />
            <rect x="50" y="80" width="15" height="15" rx="3" fill="#e0e0e0" />

            <!-- The Person -->
            <path d="M180 230 Q180 150 250 150 Q320 150 320 230" fill="#1D56A5" /> <!-- Shirt -->
            <circle cx="250" cy="110" r="40" fill="#fbd38d" /> <!-- Face -->
            <path d="M210 110 Q210 60 250 60 Q290 60 290 110" fill="#333" /> <!-- Hair -->
            
            <!-- Eyes & Mouth (Sad) -->
            <circle cx="235" cy="110" r="3" fill="#333" />
            <circle cx="265" cy="110" r="3" fill="#333" />
            <path d="M240 135 Q250 125 260 135" stroke="#333" stroke-width="2" fill="none" />

            <!-- Laptop -->
            <path d="M240 230 L360 230 L380 170 L260 170 Z" fill="#4a4a4a" /> <!-- Base -->
            <rect x="270" y="175" width="100" height="50" rx="2" fill="#333" /> <!-- Screen Area -->

            <!-- Thought Bubble -->
            <path d="M360 120 Q360 70 420 70 Q480 70 480 120 Q480 170 420 170 L400 190 L400 170 Q360 170 360 120" fill="white" stroke="#e0e0e0" stroke-width="2" />
            
            <!-- No Data Document -->
            <rect x="395" y="90" width="50" height="65" rx="4" fill="#f8f9fa" stroke="#1D56A5" stroke-width="2" />
            <path d="M410 110 L430 110 M415 125 Q420 120 425 125" stroke="#1D56A5" stroke-width="3" stroke-linecap="round" fill="none" />
            <text x="400" y="145" font-family="Arial" font-size="8" font-weight="bold" fill="#1D56A5">NO DATA</text>

            <!-- Animated Question Marks -->
            <g class="q-marks">
                <text x="210" y="50" font-family="Arial" font-size="24" font-weight="bold" fill="#1D56A5">?</text>
                <text x="240" y="35" font-family="Arial" font-size="28" font-weight="bold" fill="#1D56A5">?</text>
                <text x="280" y="55" font-family="Arial" font-size="24" font-weight="bold" fill="#1D56A5">?</text>
            </g>
        </svg>
                <div class="empty-state-text">No Notifications Found</div>
            </div>
        </li>
    </ul>
</li>

<!-- 7. Chats -->
<!-- REMOVED 'messages-menu' class -->
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
        <!-- Large Theme Bubble -->
        <path class="bubble-1" d="M14.5 48.5C14.5 24.4756 35.7665 5 62 5C88.2335 5 109.5 24.4756 109.5 48.5C109.5 72.5244 88.2335 92 62 92C56.634 92 51.5033 91.1852 46.7329 89.6808L24.5 98V77.7288C18.4239 70.3644 14.5 60.103 14.5 48.5Z" fill="#ffca3b"/>
        
        <!-- Lines inside -->
        <rect x="38" y="32" width="50" height="6" rx="3" fill="white" fill-opacity="0.4"/>
        <rect x="38" y="46" width="50" height="6" rx="3" fill="white" fill-opacity="0.4"/>
        <rect x="38" y="60" width="30" height="6" rx="3" fill="white" fill-opacity="0.4"/>

        <!-- Small Accent Bubble -->
        <path class="bubble-2" d="M125 76.5C125 58.5507 109.106 44 89.5 44C69.8939 44 54 58.5507 54 76.5C54 94.4493 69.8939 109 89.5 109C93.5015 109 97.3323 108.384 100.893 107.253L117.5 113.5V98.2619C122.031 92.7301 125 85.0471 125 76.5Z" fill="#E6B51C"/>
    </svg>
    <div class="empty-state-text">No Recent Chats</div>
</div>

        </li>
    </ul>
</li>

<!-- 8. User Profile -->
<!-- REMOVED 'user-menu' class -->
<li class="dropdown user">
    <a href="#" class="dropdown-toggle user-profile-link" data-toggle="dropdown">
        <img src="/images/icon/150.jpeg" class="user-image" alt="User Image">
        <div class="user-info-text hidden-xs">
            <span class="user-name">Administrator</span>
            <span class="user-role">Admin</span>
        </div>
    </a>
    <ul class="dropdown-menu user-menu-dropdown">
        <li>
            <a href="/profile/edit">
                <i class="fa fa-user-o"></i> Edit Profile
            </a>
        </li>
        <li>
            <a href="/logout" onClick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out"></i> Logout
            </a>
            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                <!-- Add your CSRF token -->
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
        
        
        

        @include('partials.menu')

        <div class="content-wrapper" style="min-height: 960px;">
        
        
        
        <div class="container-fluid">
        
        
        <div class="admin-header clearfix">
        
        <!-- Left Side -->
        
        
        
        <div class="pull-left greeting-text">
    <h3>Hello, Administrator 👋</h3>
    <!-- The Sliding Container -->
    <div class="sliding-paragraph-box">
        <div class="sliding-inner">
            <p>🌈 Let’s make today productive and successful! 🏆</p>
            <p>🧠 Let’s brainstorm and create something awesome! 💡</p>
            <!-- Loop the first one for a seamless transition -->
            <p>🌈 Let’s make today productive and successful! 🏆</p>
        </div>
    </div>
</div>

        <!-- Right Side -->
        <div class="pull-right text-right">
            <div class="sort-pill-container shadow-sm">
                <span class="sort-label">Sort By</span>
                <div class="dropdown custom-select-container">
    <!-- The visible "Select" box -->
    <button class="select-styled dropdown-toggle" type="button" data-toggle="dropdown">
        <span class="selected-text">This Year</span>
        <i class="fa fa-chevron-down pull-right chevron-icon"></i>
    </button>
    
    <!-- The dropdown list -->
    <ul class="dropdown-menu select-dropdown-list">
        <li><a href="#">Today</a></li>
        <li><a href="#">This Week</a></li>
        <li><a href="#">This Month</a></li>
        <li class="active"><a href="#">This Year</a></li>
        <li><a href="#">Custom</a></li>
    </ul>
</div>
            </div>
        </div>

    </div>
        
        
        
        </div>
        
        
        
        
        
            @if (session('message'))
                <div class="row" style='padding:20px 20px 0 20px;'>
                    <div class="col-lg-12">
                        <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                    </div>
                </div>
            @endif
            @if ($errors->count() > 0)
                <div class="row" style='padding:20px 20px 0 20px;'>
                    <div class="col-lg-12">
                        <div class="alert alert-danger">
                            <ul class="list-unstyled">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
        <footer class="main-footer text-center">
            <strong>{{ $siteName }} &copy;</strong>
            {{ trans('global.allRightsReserved') }}
            Powered by
            <a href="https://unibooker.app/" target="_blank">UniBooker.app</a>
            |
            Load time: {{ number_format(microtime(true) - LARAVEL_START, 2) }}s
        </footer>

        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        $(function() {
            let copyButtonTrans = '{{ trans('global.copy') }}'
            let csvButtonTrans = '{{ trans('global.csv') }}'
            let excelButtonTrans = '{{ trans('global.excel') }}'
            let pdfButtonTrans = '{{ trans('global.pdf') }}'
            let printButtonTrans = '{{ trans('global.print') }}'
            let colvisButtonTrans = '{{ trans('global.colvis') }}'
            let selectAllButtonTrans = '{{ trans('global.select_all') }}'
            let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'

            let languages = {
                'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json',
                'ar': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Arabic.json',
                'fr': 'https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json'
            };

            $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {
                className: 'btn'
            })
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    url: languages['{{ app()->getLocale() }}']
                },
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }, {
                    orderable: false,
                    searchable: false,
                    targets: -1
                }],
                select: {
                    style: 'multi+shift',
                    selector: 'td:first-child'
                },
                order: [],
                scrollX: true,
                pageLength: 100,
                dom: 'lBfrtip<"actions">',
                buttons: [{
                        extend: 'selectAll',
                        className: 'btn-primary',
                        text: selectAllButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        },
                        action: function(e, dt) {
                            e.preventDefault()
                            dt.rows().deselect();
                            dt.rows({
                                search: 'applied'
                            }).select();
                        }
                    },
                    {
                        extend: 'selectNone',
                        className: 'btn-primary',
                        text: selectNoneButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn-default',
                        text: copyButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-default',
                        text: csvButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-default',
                        text: excelButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-default',
                        text: pdfButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn-default',
                        text: printButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        className: 'btn-default',
                        text: colvisButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

            $.fn.dataTable.ext.classes.sPageButton = '';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '{{ trans('global.areYouSure') }}',
                text: '{{ trans('global.Arewantodeletethis') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ trans('global.yes') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form with the specified ID
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#daterange-btn').daterangepicker({
                opens: 'right',
                autoUpdateInput: false,
                ranges: {
                    'Anytime': [moment(), moment()],
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom Range'
                }
            });
            const storedStartDate = localStorage.getItem('selectedStartDate');
            const storedEndDate = localStorage.getItem('selectedEndDate');
            const urlFrom = "{{ request()->input('from') }}";
            const urlTo = "{{ request()->input('to') }}";
            if (storedStartDate && storedEndDate && urlFrom && urlTo) {
                const startDate = moment(storedStartDate);
                const endDate = moment(storedEndDate);
                $('#daterange-btn').data('daterangepicker').setStartDate(startDate);
                $('#daterange-btn').data('daterangepicker').setEndDate(endDate);
                $('#daterange-btn').val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
            } else {
                $('#daterange-btn').val('');
                $('#startDate').val('');
                $('#endDate').val('');
                localStorage.removeItem('selectedStartDate');
                localStorage.removeItem('selectedEndDate');
            }
            $('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
                $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));
                localStorage.setItem('selectedStartDate', picker.startDate.format('YYYY-MM-DD'));
                localStorage.setItem('selectedEndDate', picker.endDate.format('YYYY-MM-DD'));
            });
            $('#daterange-btn').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#startDate').val('');
                $('#endDate').val('');
                localStorage.removeItem('selectedStartDate');
                localStorage.removeItem('selectedEndDate');
            });
            var storedModuleId = localStorage.getItem('module_id');
            if (storedModuleId) {
                $('#module_id_input').val(storedModuleId);
            }

            $('.module-popup-item').on('click', function(event) {
                event.preventDefault();
                var moduleId = $(this).data('module-id');
                var moduleUrl = $(this).data('url');
                var filterType = $(this).data('filter');
                var requestData = {
                    'status': '1',
                    'pid': moduleId,
                    'type': 'default_module',
                    'module_id': moduleId
                };

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                requestData['_token'] = csrfToken;
                $.ajax({
                    url: '/admin/update-module-status',
                    type: 'POST',
                    data: requestData,
                    success: function(data) {
                        $('#module_id_input').val(moduleId);
                        localStorage.setItem('module_id', moduleId);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error: ' + status);
                    }
                });
            });
        });

        $(document).ready(function() {
            @if (session('error'))
                toastr.error("{{ session('error') }}", 'Error', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-bottom-right"
                });
            @endif

            @if (session('success'))
                toastr.success("{{ session('success') }}", 'Success', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-bottom-right"
                });
            @endif
        });

        function pageLoader() {
            return {
                bar: null,
                init() {
                    this.bar = this.$refs.bar;
                    setTimeout(() => {
                        document.addEventListener('click', e => {
                            const link = e.target.closest('a');
                            if (!link) return;
                            const href = link.getAttribute('href');
                            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                            e.preventDefault();
                            this.start(href);
                        });
                    }, 50);
                },
                start(url) {
                    this.bar.style.width = '0%';
                    this.bar.style.transition = 'width 0.5s ease-out';
                    setTimeout(() => this.bar.style.width = '50%', 10);
                    window.location.href = url;
                }
            }
        }
        window.addEventListener('beforeunload', () => {
            const bar = document.querySelector('[x-ref="bar"]');
            if (bar) bar.style.width = '50%';
        });
    </script>

    @yield('scripts')
    <script src="{{ asset('js/resources/main.js') }}?{{ time() }}"></script>
    
    
    
    <!-- Put this at the bottom of your layout file, after adminlte.js is loaded -->
<script>
    $(document).ready(function () {
        // 1. Manually initialize the Sidebar Tree logic
        // This forces the accordion to work even if the Header JS crashed
        if (typeof $.fn.tree === 'function') {
            $('.sidebar-menu').tree();
        }

        // 2. Optional: Fix Dropdowns not closing on click
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').hide();
            }
        });
        
        // 3. Optional: Manually toggle dropdowns if bootstrap JS is conflicted
        $('.dropdown-toggle').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var $el = $(this).next('.dropdown-menu');
            var isVisible = $el.is(':visible');
            
            // Close all other dropdowns
            $('.dropdown-menu').hide();
            
            // Toggle this one
            if(!isVisible) {
                $el.show();
            }
        });
    });
</script>


<script>

$('.select-dropdown-list li a').on('click', function(){
    var text = $(this).text();
    $(this).closest('.dropdown').find('.selected-text').text(text);
    $(this).parent().addClass('active').siblings().removeClass('active');
});
</script>

</body>

</html>

