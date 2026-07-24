<div class="btn-group" style="margin-bottom: 15px;">
    <a href="{{ route('admin.food-catalog.restaurants.index') }}" class="btn btn-default {{ request()->routeIs('admin.food-catalog.restaurants.index') ? 'btn-primary' : '' }}">Restaurants</a>
    <a href="{{ route('admin.food-catalog.branches.index') }}" class="btn btn-default {{ request()->routeIs('admin.food-catalog.branches.index') ? 'btn-primary' : '' }}">Branches</a>
    <a href="{{ route('admin.food-catalog.categories.index') }}" class="btn btn-default {{ request()->routeIs('admin.food-catalog.categories.index') ? 'btn-primary' : '' }}">Categories</a>
    <a href="{{ route('admin.food-catalog.items.index') }}" class="btn btn-default {{ request()->routeIs('admin.food-catalog.items.index') || request()->routeIs('admin.food-catalog.items.options') ? 'btn-primary' : '' }}">Items</a>
</div>
