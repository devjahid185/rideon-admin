<div style="margin-left: 5px;" class="row">
    <div class="col-lg-12">

        <a class="{{ !request()->has('status') || request()->query('status') === '' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.ticket.index') }}">
            All
            @if($statusCounts['all'] > 0)
            <span class="badge badge-light">{{ $statusCounts['all'] }}</span>
            @else
            <span class="badge badge-light">0</span>
            @endif
        </a>

        <!-- Open (status = 1) -->
        <a class="{{ request()->query('status') == '1' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.ticket.index', ['status' => 1]) }}">
            Open @if($statusCounts['open'] > 0)
            <span class="badge badge-light">{{ $statusCounts['open'] }}</span>
            @else
            <span class="badge badge-light">0</span>
            @endif
        </a>

        <!-- Closed (status = 0) -->
        <a class="{{ request()->query('status') == '0' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.ticket.index', ['status' => 0]) }}">
            Closed @if($statusCounts['closed'] > 0)
            <span class="badge badge-light">{{ $statusCounts['closed'] }}</span>
            @else
            <span class="badge badge-light">0</span>
            @endif
        </a>
    </div>
</div>