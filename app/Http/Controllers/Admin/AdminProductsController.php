<?php

namespace App\Http\Controllers\admin;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\AdminProducts;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\ProductVariation;
use App\Models\Size;

class AdminProductsController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::all();
        $categories = Category::all();
        $listProducts = AdminProducts::with(['category', 'images', 'variations.size', 'variations.color'])
            ->withTrashed()
            ->select('products.*')
            ->get();

        $query = AdminProducts::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('price_range')) {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) === 2) {
                $query->whereBetween('price_sale', [$priceRange[0], $priceRange[1]]);
            } elseif ($priceRange[0] === '500000+') {
                $query->where('price_sale', '>', 500000);
            }
        }

        $listProducts = $query->paginate(10);

        return view('admin.Products.index', compact('listProducts', 'categories', 'brands'));
    }

    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();
        $sizes = Size::all();
        $colors = Color::all();

        return view('admin.products.create', compact('categories', 'brands', 'sizes', 'colors'));
    }

    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'price_sale' => 'nullable|numeric',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'variation.size.*' => 'required|exists:sizes,id',
            'variation.color.*' => 'required|exists:colors,id',
            'variation.quantity.*' => 'required|numeric',
            'variation.price.*' => 'required|numeric',
            'variation.image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        // Lưu sản phẩm chính
        $product = AdminProducts::create($request->only(['name', 'price', 'price_sale', 'description', 'category_id', 'brand_id']));
        $this->uploadImages($request, $product->id);
        // Lưu dữ liệu biến thể
        $sizes = $request->input('variation.size');
        $colors = $request->input('variation.color');
        $quantities = $request->input('variation.quantity');
        $prices = $request->input('variation.price');
        $images = $request->file('variation.image');

        foreach ($sizes as $index => $size) {
            $color = $colors[$index];
            $quantity = $quantities[$index];
            $price = $prices[$index];
            $imagePath = null;

            if (isset($images[$index]) && $images[$index]->isValid()) {
                $imagePath = $images[$index]->store('images/product_variations', 'public');
            }

            ProductVariation::create([
                'product_id' => $product->id,
                'size_id' => $size,
                'color_id' => $color,
                'quantity' => $quantity,
                'price' => $price,
                'image' => $imagePath,
            ]);
        }

        return redirect()->route('admin-products.index')->with('success', 'Sản phẩm đã được thêm thành công!');
    }





    public function show($id)
    {
        $product = AdminProducts::with(['variations'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }


    public function edit($id)
    {
        $brands = Brand::all();
        $product = AdminProducts::with(['images', 'variations.size', 'variations.color'])->findOrFail($id);
        $categories = Category::all();
        $sizes = Size::all();
        $colors = Color::all();

        return view('admin.products.edit', compact('brands', 'product', 'categories', 'sizes', 'colors'));
    }

    public function update(Request $request, $id)
    {
        $product = AdminProducts::findOrFail($id);

        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'price_sale' => 'nullable|numeric',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'variation.size.*' => 'required|exists:sizes,id',
            'variation.color.*' => 'required|exists:colors,id',
            'variation.quantity.*' => 'required|integer|min:0',
            'variation.price.*' => 'required|numeric',
            'variation.id.*' => 'required|exists:product_variations,id',
        ]);

        $product->update($request->only(['name', 'price', 'price_sale', 'description', 'category_id', 'brand_id']));

        if ($request->hasFile('images')) {
            foreach ($product->images as $image) {
                Storage::delete($image->path);
                $image->delete();
            }

            foreach ($request->file('images') as $file) {
                $path = $file->store('products');
                $product->images()->create(['path' => $path]);
            }
        }

        // Update variations
        $variations = $request->input('variation');
        foreach ($variations['size'] as $index => $size) {
            $product->variations()->updateOrCreate(
                ['id' => $variations['id'][$index]],
                [
                    'size_id' => $size,
                    'color_id' => $variations['color'][$index],
                    'quantity' => $variations['quantity'][$index],
                    'price' => $variations['price'][$index],
                ]
            );
        }

        return redirect()->route('admin-products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function destroy(string $id)
    {
        $product = AdminProducts::findOrFail($id);
        $product->delete();

        return redirect()->route('admin-products.index')->with('success', 'Sản phẩm đã được xóa thành công.');
    }

    protected function uploadImages(Request $request, $productId)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images/products', 'public');
                ProductImage::create([
                    'product_id' => $productId,
                    'image_path' => $path,
                ]);
            }
        }
    }



}





