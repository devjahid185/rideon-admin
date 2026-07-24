@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Food Catalog: Categories</div>
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

                        <form method="POST" action="{{ route('admin.food-catalog.categories.store') }}" class="row" style="margin-bottom: 16px;">
                            @csrf
                            <input type="hidden" name="section" value="categories">
                            <div class="col-md-3">
                                <select name="restaurant_id" class="form-control" required>
                                    <option value="">Restaurant</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4"><input name="name" class="form-control" placeholder="Category Name" required></div>
                            <div class="col-md-3"><input name="sort_order" class="form-control" placeholder="Sort Order"></div>
                            <div class="col-md-2"><button class="btn btn-success btn-block">Add Category</button></div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead><tr><th>ID</th><th>Restaurant</th><th>Name</th><th>Sort</th><th>Action</th></tr></thead>
                                <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ optional($category->restaurant)->name }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->sort_order }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.food-catalog.categories.delete', $category->id) }}" onsubmit="return confirm('Delete category?')" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="section" value="categories">
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
