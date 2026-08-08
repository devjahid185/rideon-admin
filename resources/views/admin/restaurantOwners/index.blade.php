@extends('layouts.admin')

@section('content')
    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-3">
                <div class="small-box bg-aqua">
                    <div class="inner"><h3>{{ $counts['total'] }}</h3><p>Total Owners</p></div>
                    <div class="icon"><i class="fa fa-users"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-green">
                    <div class="inner"><h3>{{ $counts['active'] }}</h3><p>Active Accounts</p></div>
                    <div class="icon"><i class="fa fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-yellow">
                    <div class="inner"><h3>{{ $counts['inactive'] }}</h3><p>Inactive Accounts</p></div>
                    <div class="icon"><i class="fa fa-pause-circle"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-purple">
                    <div class="inner"><h3>{{ $counts['with_restaurant'] }}</h3><p>With Restaurant</p></div>
                    <div class="icon"><i class="fa fa-store"></i></div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Restaurant Owners</strong>
                <span class="text-muted">Manage owner accounts, restaurants, sales and access from one page.</span>
            </div>
            <div class="panel-body">
                <form method="GET" class="row" style="margin-bottom: 16px;">
                    <div class="col-md-5">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, email or phone">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a href="{{ route('admin.restaurant-owners.index') }}" class="btn btn-default">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Owner</th>
                                <th>Restaurant</th>
                                <th>Food Orders</th>
                                <th>Sales</th>
                                <th>Wallet</th>
                                <th>Status</th>
                                <th style="width: 170px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($owners as $owner)
                                @php
                                    $restaurant = $restaurants[$owner->id] ?? null;
                                    $stats = $restaurant ? ($orderStats[$restaurant->id] ?? null) : null;
                                    $wallet = $walletStats[$owner->id] ?? null;
                                    $credit = (float) ($wallet->total_credit ?? 0);
                                    $debit = (float) ($wallet->total_debit ?? 0);
                                    $balance = $credit - $debit;
                                @endphp
                                <tr>
                                    <td>{{ $owner->id }}</td>
                                    <td>
                                        <strong>{{ trim(($owner->first_name ?? '') . ' ' . ($owner->last_name ?? '')) ?: 'No name' }}</strong><br>
                                        <span class="text-muted">{{ $owner->email ?: 'No email' }}</span><br>
                                        <span>{{ trim(($owner->phone_country ?? '') . ' ' . ($owner->phone ?? '')) ?: 'No phone' }}</span>
                                    </td>
                                    <td>
                                        @if($restaurant)
                                            <strong>{{ $restaurant->name }}</strong>
                                            <br><span class="text-muted">{{ $restaurant->email ?: $restaurant->phone }}</span>
                                            <br><span class="label {{ $restaurant->is_active ? 'label-success' : 'label-default' }}">{{ $restaurant->is_active ? 'Active' : 'Inactive' }}</span>
                                            <span class="label label-info">{{ $restaurant->branches_count }} branches</span>
                                            <span class="label label-primary">{{ $restaurant->items_count }} items</span>
                                        @else
                                            <span class="label label-warning">No restaurant onboarded</span>
                                        @endif
                                    </td>
                                    <td>{{ (int) ($stats->orders_count ?? 0) }}</td>
                                    <td>{{ number_format((float) ($stats->total_sales ?? 0), 2) }}</td>
                                    <td>
                                        <strong>{{ number_format($balance, 2) }}</strong>
                                        <br><small class="text-muted">Credit {{ number_format($credit, 2) }} / Debit {{ number_format($debit, 2) }}</small>
                                    </td>
                                    <td>
                                        <span class="label {{ (int) $owner->status === 1 ? 'label-success' : 'label-danger' }}">
                                            {{ (int) $owner->status === 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.restaurant-owners.show', $owner->id) }}" class="btn btn-xs btn-primary">Manage</a>
                                        <form method="POST" action="{{ route('admin.restaurant-owners.toggle-status', $owner->id) }}" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-xs {{ (int) $owner->status === 1 ? 'btn-warning' : 'btn-success' }}" onclick="return confirm('Change this owner account status?')">
                                                {{ (int) $owner->status === 1 ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">No restaurant owners found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {!! $owners->links() !!}
            </div>
        </div>
    </div>
@endsection
