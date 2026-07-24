@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Food Orders Management
                    </div>
                    <div class="panel-body">
                        <form method="GET" class="form-inline" style="margin-bottom: 16px; display:flex; flex-wrap:wrap; gap:8px;">
                            <input type="date" name="from" value="{{ request('from') }}" class="form-control" />
                            <input type="date" name="to" value="{{ request('to') }}" class="form-control" />
                            <select name="restaurant_id" class="form-control">
                                <option value="">All Restaurants</option>
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}" {{ (string)$restaurantId === (string)$restaurant->id ? 'selected' : '' }}>
                                        {{ $restaurant->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach(['payment_pending','placed','accepted','preparing','ready_for_pickup','picked_up','on_the_way','delivered','cancelled'] as $st)
                                    <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.food-orders.index') }}" class="btn btn-default">Reset</a>
                        </form>

                        <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
                            <span class="btn btn-default">ALL: {{ $statusCounts['all'] ?? 0 }}</span>
                            @foreach(['payment_pending','placed','accepted','preparing','ready_for_pickup','picked_up','on_the_way','delivered','cancelled'] as $st)
                                <a href="{{ route('admin.food-orders.index', array_merge(request()->query(), ['status' => $st])) }}"
                                   class="btn {{ request('status') === $st ? 'btn-primary' : 'btn-default' }}">
                                    {{ strtoupper($st) }}: {{ $statusCounts[$st] ?? 0 }}
                                </a>
                            @endforeach
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Order Number</th>
                                        <th>Customer</th>
                                        <th>Driver</th>
                                        <th>Restaurant</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ optional($order->customer)->first_name }} {{ optional($order->customer)->last_name }}</td>
                                            <td>
                                                @if($order->driver)
                                                    {{ trim(optional($order->driver)->first_name.' '.optional($order->driver)->last_name) ?: 'Driver #'.$order->driver->id }}
                                                    <br>
                                                    <small>{{ optional($order->driver)->phone_country }}{{ optional($order->driver)->phone }}</small>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>{{ optional($order->restaurant)->name }}</td>
                                            <td><span class="label label-info">{{ $order->status }}</span></td>
                                            <td>{{ $order->payment_method }} / {{ $order->payment_status }}</td>
                                            <td>{{ number_format((float)$order->total_amount, 2) }}</td>
                                            <td>{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.food-orders.show', $order->id) }}" class="btn btn-xs btn-primary">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No food orders found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
