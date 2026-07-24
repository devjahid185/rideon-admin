   <header class="main-header cvvv">
            <a href="/admin/" class="logo">
                <span class="logo-mini">
                    @if (isset($logoPath) && !empty($logoPath) && file_exists(public_path($logoPath)))
                        <img src="{{ $logoPath }}" alt="{{ $siteName ?? trans('global.site_title') }}" />
                    @else
                        <b>{{ $siteName ?? trans('global.site_title') }}</b>
                    @endif
                </span>
                <span class="logo-lg">
                    @if (isset($logoPath) && !empty($logoPath) && file_exists(public_path($logoPath)))
                        <img src="{{ $logoPath }}" alt="{{ $siteName ?? trans('global.site_title') }}" />
                    @else
                        {{ $siteName ?? trans('global.site_title') }}
                    @endif
                </span>
            </a>

            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">{{ trans('global.toggleNavigation') }}</span>
                </a>

                <div class="navbar-custom-menu" style="display: none">
                    <ul class="nav navbar-nav">

                        @can('language_setting_access')
                            @if (count(config('global.available_languages', [])) > 1)
                                <li class="dropdown notifications-menu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        {{ strtoupper(app()->getLocale()) }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <ul class="menu">
                                                @foreach (config('global.available_languages') as $langLocale => $langName)
                                                    <li>
                                                        <a
                                                            href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }}
                                                            ({{ $langName }})
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        @endcan
                    </ul>
                </div>

            </nav>
        </header>