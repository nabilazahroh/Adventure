<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('category')->orderBy('name')->get();

        return view('categories.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => ['nullable', 'image', 'max:2048'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name' => $request->name,
            'image_path' => $imagePath,
        ]);

        $productIds = $request->input('product_ids', []);
        if (!empty($productIds)) {
            Product::whereIn('id', $productIds)->update([
                'category_id' => $category->id,
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load([
            'products' => function ($query) {
                $query->orderBy('name');
            },
        ])->loadCount('products');

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category->loadCount('products');
        $products = Product::with('category')->orderBy('name')->get();
        $selectedProductIds = $category->products()->pluck('id')->all();

        return view('categories.edit', compact('category', 'products', 'selectedProductIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => ['nullable', 'image', 'max:2048'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $imagePath = $category->image_path;
        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('categories', 'public');
            if (!empty($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $newPath;
        }

        $category->update([
            'name' => $request->name,
            'image_path' => $imagePath,
        ]);

        $selectedIds = $request->input('product_ids', []);

        if (empty($selectedIds)) {
            Product::where('category_id', $category->id)
                ->update(['category_id' => null]);
        } else {
            Product::where('category_id', $category->id)
                ->whereNotIn('id', $selectedIds)
                ->update(['category_id' => null]);

            Product::whereIn('id', $selectedIds)->update([
                'category_id' => $category->id,
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk!');
        }

        if (!empty($category->image_path)) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
