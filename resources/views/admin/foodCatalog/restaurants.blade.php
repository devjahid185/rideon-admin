@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Food Catalog: Restaurants</div>
                    <div class="panel-body">
                        @include('admin.foodCatalog._tabs')
                        <form method="POST" action="{{ route('admin.food-catalog.restaurants.store') }}" class="row" enctype="multipart/form-data" style="margin-bottom: 16px;">
                            @csrf
                            <input type="hidden" name="section" value="restaurants">
                            <div class="col-md-3"><input name="name" class="form-control" placeholder="Name" required></div>
                            <div class="col-md-2"><input name="phone" class="form-control" placeholder="Phone"></div>
                            <div class="col-md-3"><input name="email" type="email" class="form-control" placeholder="Email"></div>
                            <div class="col-md-2"><input name="rating" class="form-control" placeholder="Rating (0-5)"></div>
                            <div class="col-md-2"><input type="file" name="logo_image_file" class="form-control"></div>
                            <div class="col-md-2" style="margin-top: 8px;"><button class="btn btn-success btn-block">Add Restaurant</button></div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Rating</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody>
                                @foreach($restaurants as $restaurant)
                                    <tr>
                                        <td>{{ $restaurant->id }}</td>
                                        <td>{{ $restaurant->name }}</td>
                                        <td>{{ $restaurant->phone }}</td>
                                        <td>{{ $restaurant->email }}</td>
                                        <td>{{ $restaurant->rating }}</td>
                                        <td>{{ $restaurant->is_active ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.food-catalog.restaurants.update', $restaurant->id) }}" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="section" value="restaurants">
                                                <input type="hidden" name="name" value="{{ $restaurant->name }}">
                                                <input type="hidden" name="phone" value="{{ $restaurant->phone }}">
                                                <input type="hidden" name="email" value="{{ $restaurant->email }}">
                                                <input type="hidden" name="description" value="{{ $restaurant->description }}">
                                                <input type="hidden" name="rating" value="{{ $restaurant->rating }}">
                                                <input type="hidden" name="is_active" value="{{ $restaurant->is_active ? 0 : 1 }}">
                                                <button class="btn btn-xs btn-warning">{{ $restaurant->is_active ? 'Deactivate' : 'Activate' }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.food-catalog.restaurants.delete', $restaurant->id) }}" onsubmit="return confirm('Delete restaurant?')" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="section" value="restaurants">
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
