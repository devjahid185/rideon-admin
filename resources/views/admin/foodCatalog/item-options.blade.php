@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Item Options: {{ $item->name }}</div>
                    <div class="panel-body">
                        @include('admin.foodCatalog._tabs')
                        <p><strong>Restaurant:</strong> {{ optional($item->restaurant)->name }} | <strong>Category:</strong> {{ optional($item->category)->name }}</p>
                        <a href="{{ route('admin.food-catalog.items.index') }}" class="btn btn-default" style="margin-bottom: 12px;">Back to Items</a>

                        <div class="row">
                            <div class="col-md-6">
                                <h4>Variants</h4>
                                <form method="POST" action="{{ route('admin.food-catalog.variants.store') }}" class="form-inline" style="margin-bottom: 10px;">
                                    @csrf
                                    <input type="hidden" name="food_item_id" value="{{ $item->id }}">
                                    <input name="name" class="form-control" placeholder="Variant name" required>
                                    <input name="price_delta" class="form-control" placeholder="Price delta" required>
                                    <button class="btn btn-success">Add</button>
                                </form>
                                <table class="table table-bordered table-striped">
                                    <thead><tr><th>Name</th><th>Delta</th><th>Status</th><th>Action</th></tr></thead>
                                    <tbody>
                                    @foreach($item->variants as $variant)
                                        <tr>
                                            <td>{{ $variant->name }}</td>
                                            <td>{{ $variant->price_delta }}</td>
                                            <td>{{ $variant->is_active ? 'Active' : 'Inactive' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.food-catalog.variants.delete', $variant->id) }}" style="display:inline;" onsubmit="return confirm('Delete variant?')">
                                                    @csrf
                                                    <button class="btn btn-xs btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4>Addons</h4>
                                <form method="POST" action="{{ route('admin.food-catalog.addons.store') }}" class="form-inline" style="margin-bottom: 10px;">
                                    @csrf
                                    <input type="hidden" name="food_item_id" value="{{ $item->id }}">
                                    <input name="name" class="form-control" placeholder="Addon name" required>
                                    <input name="price" class="form-control" placeholder="Price" required>
                                    <button class="btn btn-success">Add</button>
                                </form>
                                <table class="table table-bordered table-striped">
                                    <thead><tr><th>Name</th><th>Price</th><th>Status</th><th>Action</th></tr></thead>
                                    <tbody>
                                    @foreach($item->addons as $addon)
                                        <tr>
                                            <td>{{ $addon->name }}</td>
                                            <td>{{ $addon->price }}</td>
                                            <td>{{ $addon->is_active ? 'Active' : 'Inactive' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.food-catalog.addons.delete', $addon->id) }}" style="display:inline;" onsubmit="return confirm('Delete addon?')">
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
    </div>
@endsection
