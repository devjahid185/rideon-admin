@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Food Catalog: Branches</div>
                    <div class="panel-body">
                        @include('admin.foodCatalog._tabs')

                        <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                            <select name="restaurant_id" class="form-control">
                                <option value="">All Restaurants</option>
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}" {{ (string)$restaurantId === (string)$restaurant->id ? 'selected' : '' }}>{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </form>

                        <form method="POST" action="{{ route('admin.food-catalog.branches.store') }}" class="row" style="margin-bottom: 16px;">
                            @csrf
                            <input type="hidden" name="section" value="branches">
                            <div class="col-md-2">
                                <select name="restaurant_id" class="form-control" required>
                                    <option value="">Restaurant</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><input name="name" class="form-control" placeholder="Branch Name" required></div>
                            <div class="col-md-2"><input name="contact_phone" class="form-control" placeholder="Phone"></div>
                            <div class="col-md-2"><input name="latitude" type="number" step="any" class="form-control" placeholder="Latitude" required></div>
                            <div class="col-md-2"><input name="longitude" type="number" step="any" class="form-control" placeholder="Longitude" required></div>
                            <div class="col-md-2"><input name="delivery_radius_km" class="form-control" placeholder="KM"></div>
                            <div class="col-md-12" style="margin-top: 8px;"><input name="address" class="form-control" placeholder="Address" required></div>
                            <div class="col-md-2" style="margin-top: 8px;"><button class="btn btn-success btn-block">Add Branch</button></div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead><tr><th>ID</th><th>Restaurant</th><th>Name</th><th>Address</th><th>Phone</th><th>Latitude</th><th>Longitude</th><th>Action</th></tr></thead>
                                <tbody>
                                @foreach($branches as $branch)
                                    <tr>
                                        <td>{{ $branch->id }}</td>
                                        <td>{{ optional($branch->restaurant)->name }}</td>
                                        <td>{{ $branch->name }}</td>
                                        <td>{{ $branch->address }}</td>
                                        <td>{{ $branch->contact_phone }}</td>
                                        <td>{{ $branch->latitude }}</td>
                                        <td>{{ $branch->longitude }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.food-catalog.branches.delete', $branch->id) }}" onsubmit="return confirm('Delete branch?')" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="section" value="branches">
                                                <button class="btn btn-xs btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
