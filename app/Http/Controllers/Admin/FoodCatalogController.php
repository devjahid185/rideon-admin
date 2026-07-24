<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modern\FoodAddon;
use App\Models\Modern\FoodCategory;
use App\Models\Modern\FoodItem;
use App\Models\Modern\FoodItemVariant;
use App\Models\Modern\Restaurant;
use App\Models\Modern\RestaurantBranch;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FoodCatalogController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.food-catalog.restaurants.index');
    }

    public function restaurantsPage(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $restaurants = Restaurant::query()
            ->when($restaurantId, fn ($q) => $q->where('id', $restaurantId))
            ->latest('id')
            ->get();

        return view('admin.foodCatalog.restaurants', compact('restaurantId', 'restaurants'));
    }

    public function branchesPage(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $restaurants = Restaurant::query()->latest('id')->get(['id', 'name']);
        $branches = RestaurantBranch::query()
            ->with('restaurant:id,name')
            ->when($restaurantId, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->latest('id')
            ->get();

        return view('admin.foodCatalog.branches', compact('restaurantId', 'restaurants', 'branches'));
    }

    public function categoriesPage(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $restaurants = Restaurant::query()->latest('id')->get(['id', 'name']);
        $categories = FoodCategory::query()
            ->with('restaurant:id,name')
            ->when($restaurantId, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->orderBy('sort_order')
            ->latest('id')
            ->get();

        return view('admin.foodCatalog.categories', compact('restaurantId', 'restaurants', 'categories'));
    }

    public function itemsPage(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $restaurants = Restaurant::query()->latest('id')->get(['id', 'name']);
        $categories = FoodCategory::query()
            ->with('restaurant:id,name')
            ->when($restaurantId, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->orderBy('sort_order')
            ->latest('id')
            ->get();
        $items = FoodItem::query()
            ->with(['restaurant:id,name', 'category:id,name'])
            ->when($restaurantId, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->latest('id')
            ->get();

        return view('admin.foodCatalog.items', compact('restaurantId', 'restaurants', 'categories', 'items'));
    }

    public function itemOptionsPage(int $id)
    {
        $item = FoodItem::query()
            ->with(['restaurant:id,name', 'category:id,name', 'variants', 'addons'])
            ->findOrFail($id);

        return view('admin.foodCatalog.item-options', compact('item'));
    }

    private function catalogRouteBySection(string $section): string
    {
        return match ($section) {
            'branches' => 'admin.food-catalog.branches.index',
            'categories' => 'admin.food-catalog.categories.index',
            'items' => 'admin.food-catalog.items.index',
            default => 'admin.food-catalog.restaurants.index',
        };
    }

    private function sectionFromRequest(Request $request, string $default): string
    {
        return (string) $request->input('section', $default);
    }

    private function redirectToSection(string $section, string $message)
    {
        return redirect()->route($this->catalogRouteBySection($section))->with('success', $message);
    }

    public function storeRestaurant(Request $request)
    {
        $section = $this->sectionFromRequest($request, 'restaurants');
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'description' => 'nullable|string|max:5000',
            'cover_image' => 'nullable|string|max:500',
            'logo_image' => 'nullable|string|max:500',
            'cover_image_file' => 'nullable|image|max:4096',
            'logo_image_file' => 'nullable|image|max:4096',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'nullable|boolean',
        ]);
        if ($request->hasFile('cover_image_file')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image_file'));
        }
        if ($request->hasFile('logo_image_file')) {
            $data['logo_image'] = $this->storeImage($request->file('logo_image_file'));
        }
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        Restaurant::create($data);

        return $this->redirectToSection($section, 'Restaurant created');
    }

    public function updateRestaurant(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'restaurants');
        $restaurant = Restaurant::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'description' => 'nullable|string|max:5000',
            'cover_image' => 'nullable|string|max:500',
            'logo_image' => 'nullable|string|max:500',
            'cover_image_file' => 'nullable|image|max:4096',
            'logo_image_file' => 'nullable|image|max:4096',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'nullable|boolean',
        ]);
        if ($request->hasFile('cover_image_file')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image_file'));
        }
        if ($request->hasFile('logo_image_file')) {
            $data['logo_image'] = $this->storeImage($request->file('logo_image_file'));
        }
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $restaurant->update($data);

        return $this->redirectToSection($section, 'Restaurant updated');
    }

    public function deleteRestaurant(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'restaurants');
        Restaurant::findOrFail($id)->delete();

        return $this->redirectToSection($section, 'Restaurant deleted');
    }

    public function storeBranch(Request $request)
    {
        $section = $this->sectionFromRequest($request, 'branches');
        $data = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:191',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'delivery_radius_km' => 'nullable|numeric|min:0',
            'min_delivery_time_minutes' => 'nullable|integer|min:0',
            'max_delivery_time_minutes' => 'nullable|integer|min:0',
            'is_open' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_open'] = (bool) ($data['is_open'] ?? true);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        RestaurantBranch::create($data);

        return $this->redirectToSection($section, 'Branch created');
    }

    public function updateBranch(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'branches');
        $branch = RestaurantBranch::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'delivery_radius_km' => 'nullable|numeric|min:0',
            'min_delivery_time_minutes' => 'nullable|integer|min:0',
            'max_delivery_time_minutes' => 'nullable|integer|min:0',
            'is_open' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_open'] = (bool) ($data['is_open'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $branch->update($data);

        return $this->redirectToSection($section, 'Branch updated');
    }

    public function deleteBranch(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'branches');
        RestaurantBranch::findOrFail($id)->delete();

        return $this->redirectToSection($section, 'Branch deleted');
    }

    public function storeCategory(Request $request)
    {
        $section = $this->sectionFromRequest($request, 'categories');
        $data = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        FoodCategory::create($data);

        return $this->redirectToSection($section, 'Category created');
    }

    public function updateCategory(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'categories');
        $category = FoodCategory::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $category->update($data);

        return $this->redirectToSection($section, 'Category updated');
    }

    public function deleteCategory(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'categories');
        FoodCategory::findOrFail($id)->delete();

        return $this->redirectToSection($section, 'Category deleted');
    }

    public function storeItem(Request $request)
    {
        $section = $this->sectionFromRequest($request, 'items');
        $data = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'food_category_id' => 'required|exists:food_categories,id',
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:5000',
            'base_price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|max:4096',
            'is_veg' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
        ]);
        if ($request->hasFile('image_file')) {
            $data['image'] = $this->storeImage($request->file('image_file'));
        }
        $data['is_veg'] = (bool) ($data['is_veg'] ?? false);
        $data['is_available'] = (bool) ($data['is_available'] ?? true);
        FoodItem::create($data);

        return $this->redirectToSection($section, 'Food item created');
    }

    public function updateItem(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'items');
        $item = FoodItem::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:5000',
            'base_price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|max:4096',
            'is_veg' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
        ]);
        if ($request->hasFile('image_file')) {
            $data['image'] = $this->storeImage($request->file('image_file'));
        }
        $data['is_veg'] = (bool) ($data['is_veg'] ?? false);
        $data['is_available'] = (bool) ($data['is_available'] ?? false);
        $item->update($data);

        return $this->redirectToSection($section, 'Food item updated');
    }

    public function deleteItem(Request $request, int $id)
    {
        $section = $this->sectionFromRequest($request, 'items');
        FoodItem::findOrFail($id)->delete();

        return $this->redirectToSection($section, 'Food item deleted');
    }

    public function storeVariant(Request $request)
    {
        $data = $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'name' => 'required|string|max:191',
            'price_delta' => 'required|numeric',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_default'] = (bool) ($data['is_default'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        FoodItemVariant::create($data);

        return redirect()->route('admin.food-catalog.items.options', $data['food_item_id'])->with('success', 'Variant created');
    }

    public function updateVariant(Request $request, int $id)
    {
        $variant = FoodItemVariant::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'price_delta' => 'required|numeric',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_default'] = (bool) ($data['is_default'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $variant->update($data);

        return redirect()->route('admin.food-catalog.items.options', $variant->food_item_id)->with('success', 'Variant updated');
    }

    public function deleteVariant(int $id)
    {
        $variant = FoodItemVariant::findOrFail($id);
        $foodItemId = $variant->food_item_id;
        $variant->delete();

        return redirect()->route('admin.food-catalog.items.options', $foodItemId)->with('success', 'Variant deleted');
    }

    public function storeAddon(Request $request)
    {
        $data = $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'name' => 'required|string|max:191',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        FoodAddon::create($data);

        return redirect()->route('admin.food-catalog.items.options', $data['food_item_id'])->with('success', 'Addon created');
    }

    public function updateAddon(Request $request, int $id)
    {
        $addon = FoodAddon::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $addon->update($data);

        return redirect()->route('admin.food-catalog.items.options', $addon->food_item_id)->with('success', 'Addon updated');
    }

    public function deleteAddon(int $id)
    {
        $addon = FoodAddon::findOrFail($id);
        $foodItemId = $addon->food_item_id;
        $addon->delete();

        return redirect()->route('admin.food-catalog.items.options', $foodItemId)->with('success', 'Addon deleted');
    }

    private function storeImage(UploadedFile $file): string
    {
        $path = $file->store('food', 'public');

        return asset('storage/'.$path);
    }
}
