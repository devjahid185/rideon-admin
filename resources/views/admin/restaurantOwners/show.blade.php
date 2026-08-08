@extends('layouts.admin')

@section('content')
    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin-bottom: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Restaurant Owner Account</strong>
                        <a href="{{ route('admin.restaurant-owners.index') }}" class="btn btn-default btn-xs pull-right">Back to owners</a>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ route('admin.restaurant-owners.update', $owner->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>First Name</label>
                                    <input name="first_name" class="form-control" value="{{ old('first_name', $owner->first_name) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Last Name</label>
                                    <input name="last_name" class="form-control" value="{{ old('last_name', $owner->last_name) }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Email</label>
                                    <input name="email" type="email" class="form-control" value="{{ old('email', $owner->email) }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Phone Country</label>
                                    <input name="phone_country" class="form-control" value="{{ old('phone_country', $owner->phone_country) }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Phone</label>
                                    <input name="phone" class="form-control" value="{{ old('phone', $owner->phone) }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Account Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ (string) old('status', $owner->status) === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ (string) old('status', $owner->status) === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Email Notification</label>
                                    <select name="email_notification" class="form-control">
                                        <option value="1" {{ (string) old('email_notification', $owner->email_notification) === '1' ? 'selected' : '' }}>On</option>
                                        <option value="0" {{ (string) old('email_notification', $owner->email_notification) === '0' ? 'selected' : '' }}>Off</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>SMS Notification</label>
                                    <select name="sms_notification" class="form-control">
                                        <option value="1" {{ (string) old('sms_notification', $owner->sms_notification) === '1' ? 'selected' : '' }}>On</option>
                                        <option value="0" {{ (string) old('sms_notification', $owner->sms_notification) === '0' ? 'selected' : '' }}>Off</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Push Notification</label>
                                    <select name="push_notification" class="form-control">
                                        <option value="1" {{ (string) old('push_notification', $owner->push_notification) === '1' ? 'selected' : '' }}>On</option>
                                        <option value="0" {{ (string) old('push_notification', $owner->push_notification) === '0' ? 'selected' : '' }}>Off</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary">Save Account</button>
                        </form>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Restaurant Details</strong></div>
                    <div class="panel-body">
                        @if($restaurant)
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Name:</strong> {{ $restaurant->name }}</p>
                                    <p><strong>Email:</strong> {{ $restaurant->email ?: '-' }}</p>
                                    <p><strong>Phone:</strong> {{ $restaurant->phone ?: '-' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Status:</strong> {{ $restaurant->is_active ? 'Active' : 'Inactive' }}</p>
                                    <p><strong>Rating:</strong> {{ $restaurant->rating }}</p>
                                    <p><strong>Branches:</strong> {{ $restaurant->branches->count() }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Categories:</strong> {{ $restaurant->categories->count() }}</p>
                                    <p><strong>Items:</strong> {{ $restaurant->items->count() }}</p>
                                    <a href="{{ route('admin.food-catalog.restaurants.index') }}" class="btn btn-xs btn-info">Open Food Catalog</a>
                                </div>
                            </div>
                        @else
                            <span class="label label-warning">This owner has not onboarded a restaurant yet.</span>
                        @endif
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Latest Food Orders</strong></div>
                    <div class="panel-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th><th>Created</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ trim((optional($order->customer)->first_name ?? '') . ' ' . (optional($order->customer)->last_name ?? '')) ?: ($order->customer_email ?: '-') }}</td>
                                        <td><span class="label label-info">{{ $order->status }}</span></td>
                                        <td>{{ number_format((float) $order->total_amount, 2) }}</td>
                                        <td>{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                                        <td><a href="{{ route('admin.food-orders.show', $order->id) }}" class="btn btn-xs btn-primary">View</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">No food orders found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Reset Password</strong></div>
                    <div class="panel-body">
                        <form method="POST" action="{{ route('admin.restaurant-owners.password', $owner->id) }}">
                            @csrf
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                            </div>
                            <button class="btn btn-warning btn-block" onclick="return confirm('Change password for this owner?')">Change Password</button>
                        </form>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Owner Summary</strong></div>
                    <div class="panel-body">
                        <p><strong>Owner ID:</strong> {{ $owner->id }}</p>
                        <p><strong>User Type:</strong> {{ $owner->user_type }}</p>
                        <p><strong>Created:</strong> {{ optional($owner->created_at)->format('Y-m-d H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ optional($owner->updated_at)->format('Y-m-d H:i') }}</p>
                        <p><strong>Device ID:</strong> {{ $owner->device_id ?: '-' }}</p>
                        <p><strong>FCM:</strong> {{ $owner->fcm ? 'Available' : 'Missing' }}</p>
                        <form method="POST" action="{{ route('admin.restaurant-owners.toggle-status', $owner->id) }}">
                            @csrf
                            <button class="btn {{ (int) $owner->status === 1 ? 'btn-danger' : 'btn-success' }} btn-block" onclick="return confirm('Change this owner account status?')">
                                {{ (int) $owner->status === 1 ? 'Disable Account' : 'Enable Account' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Business Numbers</strong></div>
                    <div class="panel-body">
                        <p><strong>Total Orders:</strong> {{ (int) ($orderSummary['total_orders'] ?? 0) }}</p>
                        <p><strong>Active Orders:</strong> {{ (int) ($orderSummary['active_orders'] ?? 0) }}</p>
                        <p><strong>Delivered:</strong> {{ (int) ($orderSummary['delivered_orders'] ?? 0) }}</p>
                        <p><strong>Cancelled:</strong> {{ (int) ($orderSummary['cancelled_orders'] ?? 0) }}</p>
                        <p><strong>Total Sales:</strong> {{ number_format((float) ($orderSummary['total_sales'] ?? 0), 2) }}</p>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Wallet</strong></div>
                    <div class="panel-body">
                        <p><strong>Wallet Balance:</strong> {{ number_format($wallet['wallet_balance'], 2) }}</p>
                        <p><strong>Available:</strong> {{ number_format($wallet['available_balance'], 2) }}</p>
                        <p><strong>Total Credit:</strong> {{ number_format($wallet['total_credit'], 2) }}</p>
                        <p><strong>Total Debit:</strong> {{ number_format($wallet['total_debit'], 2) }}</p>
                        <p><strong>Pending Withdrawal:</strong> {{ number_format($wallet['pending_withdrawal'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
