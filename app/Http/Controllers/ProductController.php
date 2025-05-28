<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::when($request->search, fn($query) => $query->where('name', 'LIKE', "%$request->search%"))->orderByDesc('id')->paginate()->withQueryString();

        return inertia('Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Products/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:products,slug'],
            'feature_image' => ['nullable', 'image', 'max:2048'],
            'price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0.01'],
        ]);

        if ($request->hasFile('feature_image')) {
            $path = $request->file('feature_image')->store('products', 'public');
            $validated['feature_image'] = $path;
        }

        Product::create($validated);

        return back()->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        dd($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
