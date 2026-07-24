

<div style="margin-left: 5px;" class="row">
    <div class="col-lg-12">
    <a class="{{ (request()->query('status') === 'live' && !request()->routeIs($trashRoute)) || (request()->routeIs($indexRoute) && !in_array(request()->query('status'), ['active', 'inactive', 'verified', 'featured'])) ? 'btn btn-primary' : 'btn btn-inactive' }}" 
           href="{{ route($indexRoute, ['status' => 'live']) }}">
           {{ trans('global.live') }} @if((request()->query('status') !== 'trash') && ($statusCounts['live'] > 0)) <span class="badge badge-light">{{ $statusCounts['live'] }}</span> @else <span class="badge badge-light">0</span> @endif
        </a>
        <a class="{{ request()->query('status') === 'active' ? 'btn btn-primary' : 'btn btn-inactive' }}" 
           href="{{ route($indexRoute, ['status' => 'active']) }}">
            Active @if((request()->query('status') !== 'trash') && ($statusCounts['active'] > 0)) <span class="badge badge-light">{{ $statusCounts['active'] }}</span> @else <span class="badge badge-light">0</span> @endif
        </a>
        <a class="{{ request()->query('status') === 'inactive' ? 'btn btn-primary' : 'btn btn-inactive' }}" 
           href="{{ route($indexRoute, ['status' => 'inactive']) }}">
            Inactive @if((request()->query('status') !== 'trash') && ($statusCounts['inactive'] > 0)) <span class="badge badge-light">{{ $statusCounts['inactive'] }}</span> @else <span class="badge badge-light">0</span> @endif
        </a>

        <a class="{{ request()->query('status') === 'verified' ? 'btn btn-primary' : 'btn btn-inactive' }}" 
           href="{{ route($indexRoute, ['status' => 'verified']) }}">
            Verified @if((request()->query('status') !== 'trash') && ($statusCounts['verified'] > 0)) <span class="badge badge-light">{{ $statusCounts['verified'] }}</span> @else <span class="badge badge-light">0</span> @endif
        </a>

        <a class="{{ request()->query('status') === 'featured' ? 'btn btn-primary' : 'btn btn-inactive' }}" 
           href="{{ route($indexRoute, ['status' => 'featured']) }}">
            Featured @if((request()->query('status') !== 'trash') && ($statusCounts['featured'] > 0)) <span class="badge badge-light">{{ $statusCounts['featured'] }}</span> @else <span class="badge badge-light">0</span> @endif
        </a>

        <a class="{{ request()->routeIs($trashRoute) ? 'btn btn-primary' : 'btn btn-inactive' }}" 
           href="{{ route($trashRoute) }}">
            Trash @if($statusCounts['trash'] > 0) <span class="badge badge-light">{{ $statusCounts['trash'] }}</span> @else <span class="badge badge-light">0</span> @endif
        </a>

    </div>
</div>
