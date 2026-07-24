@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Food Catalog: Items</div>
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

                        <form method="POST" action="{{ route('admin.food-catalog.items.store') }}" class="row" enctype="multipart/form-data" style="margin-bottom: 16px;">
                            @csrf
                            <input type="hidden" name="section" value="items">
                            <div class="col-md-2">
                                <select name="restaurant_id" class="form-control" required>
                                    <option value="">Restaurant</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="food_category_id" class="form-control" required>
                                    <option value="">Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }} ({{ optional($category->restaurant)->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><input name="name" class="form-control" placeholder="Item Name" required></div>
                            <div class="col-md-2"><input name="base_price" class="form-control" placeholder="Base Price" required></div>
                            <div class="col-md-2"><input type="file" name="image_file" class="form-control"></div>
                            <div class="col-md-2"><button class="btn btn-success btn-block">Add Item</button></div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead><tr><th>ID</th><th>Restaurant</th><th>Category</th><th>Name</th><th>Price</th><th>Active</th><th>Options</th><th>Action</th></tr></thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ optional($item->restaurant)->name }}</td>
                                        <td>{{ optional($item->category)->name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ number_format((float)$item->base_price, 2) }}</td>
                                        <td>{{ $item->is_available ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <a href="{{ route('admin.food-catalog.items.options', $item->id) }}" class="btn btn-xs btn-info">Variants & Addons</a>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.food-catalog.items.update', $item->id) }}" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="section" value="items">
                                                <input type="hidden" name="name" value="{{ $item->name }}">
                                                <input type="hidden" name="description" value="{{ $item->description }}">
                                                <input type="hidden" name="base_price" value="{{ $item->base_price }}">
                                                <input type="hidden" name="image" value="{{ $item->image }}">
                                                <input type="hidden" name="is_veg" value="{{ $item->is_veg ? 1 : 0 }}">
                                                <input type="hidden" name="is_available" value="{{ $item->is_available ? 0 : 1 }}">
                                                <button class="btn btn-xs btn-warning">{{ $item->is_available ? 'Disable' : 'Enable' }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.food-catalog.items.delete', $item->id) }}" onsubmit="return confirm('Delete item?')" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="section" value="items">
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
