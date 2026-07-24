@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Food Catalog Management</div>
                    <div class="panel-body">
                        <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                            <select name="restaurant_id" class="form-control">
                                <option value="">All Restaurants</option>
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}" {{ (string)$restaurantId === (string)$restaurant->id ? 'selected' : '' }}>
                                        {{ $restaurant->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" type="submit">Filter</button>
                            <a href="{{ route('admin.food-catalog.index') }}" class="btn btn-default">Reset</a>
                        </form>

                        <hr>
                        <h4>Create Restaurant</h4>
                        <form method="POST" action="{{ route('admin.food-catalog.restaurants.store') }}" class="row" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-3"><input name="name" class="form-control" placeholder="Name" required></div>
                            <div class="col-md-2"><input name="phone" class="form-control" placeholder="Phone"></div>
                            <div class="col-md-3"><input name="email" type="email" class="form-control" placeholder="Email"></div>
                            <div class="col-md-2"><input name="rating" class="form-control" placeholder="Rating (0-5)"></div>
                            <div class="col-md-2"><input type="file" name="logo_image_file" class="form-control"></div>
                            <div class="col-md-2"><button class="btn btn-success btn-block">Add</button></div>
                        </form>

                        <hr>
                        <h4>Create Branch</h4>
                        <form method="POST" action="{{ route('admin.food-catalog.branches.store') }}" class="row">
                            @csrf
                            <div class="col-md-2">
                                <select name="restaurant_id" class="form-control" required>
                                    <option value="">Restaurant</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><input name="name" class="form-control" placeholder="Branch Name" required></div>
                            <div class="col-md-3"><input name="address" class="form-control" placeholder="Address" required></div>
                            <div class="col-md-2"><input name="contact_phone" class="form-control" placeholder="Phone"></div>
                            <div class="col-md-1"><input name="delivery_radius_km" class="form-control" placeholder="KM"></div>
                            <div class="col-md-2"><button class="btn btn-success btn-block">Add</button></div>
                        </form>

                        <hr>
                        <h4>Create Category</h4>
                        <form method="POST" action="{{ route('admin.food-catalog.categories.store') }}" class="row">
                            @csrf
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
                            <div class="col-md-2"><button class="btn btn-success btn-block">Add</button></div>
                        </form>

                        <hr>
                        <h4>Create Food Item</h4>
                        <form method="POST" action="{{ route('admin.food-catalog.items.store') }}" class="row" enctype="multipart/form-data">
                            @csrf
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
                            <div class="col-md-2"><input name="image" class="form-control" placeholder="Image URL"></div>
                            <div class="col-md-2"><input type="file" name="image_file" class="form-control"></div>
                            <div class="col-md-2"><button class="btn btn-success btn-block">Add</button></div>
                        </form>

                        <hr>
                        <h4>Restaurants</h4>
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
                                                <button class="btn btn-xs btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <h4>Branches</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead><tr><th>ID</th><th>Restaurant</th><th>Name</th><th>Address</th><th>Phone</th><th>Action</th></tr></thead>
                                <tbody>
                                @foreach($branches as $branch)
                                    <tr>
                                        <td>{{ $branch->id }}</td>
                                        <td>{{ optional($branch->restaurant)->name }}</td>
                                        <td>{{ $branch->name }}</td>
                                        <td>{{ $branch->address }}</td>
                                        <td>{{ $branch->contact_phone }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.food-catalog.branches.delete', $branch->id) }}" onsubmit="return confirm('Delete branch?')" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-xs btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <h4>Categories</h4>
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
                                                <button class="btn btn-xs btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <h4>Food Items + Variant/Addons</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead><tr><th>ID</th><th>Restaurant</th><th>Category</th><th>Name</th><th>Price</th><th>Active</th><th>Variant/Addons</th><th>Action</th></tr></thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ optional($item->restaurant)->name }}</td>
                                        <td>{{ optional($item->category)->name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ number_format((float)$item->base_price, 2) }}</td>
                                        <td>{{ $item->is_available ? 'Yes' : 'No' }}</td>
                                        <td style="min-width: 280px;">
                                            <div>
                                                <strong>Variants:</strong>
                                                @foreach($item->variants as $variant)
                                                    <div style="margin: 3px 0;">
                                                        {{ $variant->name }} ({{ $variant->price_delta >= 0 ? '+' : '' }}{{ $variant->price_delta }})
                                                        <form method="POST" action="{{ route('admin.food-catalog.variants.delete', $variant->id) }}" style="display:inline;">
                                                            @csrf
                                                            <button class="btn btn-xs btn-link text-danger">x</button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                                <form method="POST" action="{{ route('admin.food-catalog.variants.store') }}" class="form-inline">
                                                    @csrf
                                                    <input type="hidden" name="food_item_id" value="{{ $item->id }}">
                                                    <input name="name" class="form-control input-sm" placeholder="Variant" required>
                                                    <input name="price_delta" class="form-control input-sm" placeholder="Delta" required>
                                                    <button class="btn btn-xs btn-success">Add</button>
                                                </form>
                                            </div>
                                            <hr style="margin:8px 0;">
                                            <div>
                                                <strong>Addons:</strong>
                                                @foreach($item->addons as $addon)
                                                    <div style="margin: 3px 0;">
                                                        {{ $addon->name }} (+{{ $addon->price }})
                                                        <form method="POST" action="{{ route('admin.food-catalog.addons.delete', $addon->id) }}" style="display:inline;">
                                                            @csrf
                                                            <button class="btn btn-xs btn-link text-danger">x</button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                                <form method="POST" action="{{ route('admin.food-catalog.addons.store') }}" class="form-inline">
                                                    @csrf
                                                    <input type="hidden" name="food_item_id" value="{{ $item->id }}">
                                                    <input name="name" class="form-control input-sm" placeholder="Addon" required>
                                                    <input name="price" class="form-control input-sm" placeholder="Price" required>
                                                    <button class="btn btn-xs btn-success">Add</button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.food-catalog.items.update', $item->id) }}" style="display:inline;">
                                                @csrf
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
@section('scripts')
<script>
    $(function () {
        $('.datatable').DataTable({
            pageLength: 25,
            order: [[0, 'desc']]
        });
    });
</script>
@endsection
