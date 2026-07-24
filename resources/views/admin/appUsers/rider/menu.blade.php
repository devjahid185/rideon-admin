<div class="driver-header">
            <div class="title">{{ trans('user.rider_detail') }} – {{ $appUser->first_name }} {{ $appUser->last_name }}</div>
            <div class="actions">
                @php
                    $navItems = [
                        ['url' => 'admin/app-users/' . $appUser->id, 'label' => trans('user.overview'), 'class' => 'btn-green'],
                        ['url' => 'admin/bookings/?customer=' .$appUser->id, 'label' => trans('user.bookings'), 'class' => 'btn-green'],
                        ['url' => 'admin/app-users/account/' . $appUser->id, 'label' => trans('user.account'), 'class' => 'btn-red', 'icon' => '⊖']
                    ];
                @endphp
                @foreach ($navItems as $item)
                    <a href="{{ url($item['url']) }}" 
                       class="btn {{ $item['class'] }} {{ request()->is($item['url']) ? 'active' : '' }}">
                        @if (!empty($item['icon'])) {{ $item['icon'] }} @endif
                        {{ $item['label'] }}
                    </a>
                @endforeach
                
            </div>
        </div>