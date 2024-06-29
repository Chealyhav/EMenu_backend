<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Product::with('categories')->get();

            return response()->json([
                'products' => $products,
                'message' => 'Products retrieved successfully',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'des' => 'required|string',
                'price' => 'required|numeric',
                'productCode' => 'required|string|max:255',
                // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,id', // Ensure all categories exist
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image->storeAs('public/', $image->hashName());
                $request['image'] = $image->hashName();
            }

            $product = Product::create($request->except('categories'));
            $product->categories()->sync($request->categories);

            return response()->json([
                'product' => $product->load('categories'),
                'message' => 'Product created successfully',
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Handle the image upload
            if ($request->hasFile('image')) {
                // Delete the old image if exists
                if ($product->image && Storage::exists('public/products/'.$product->image)) {
                    Storage::delete('public/products/'.$product->image);
                }

                // Store the new image
                $image = $request->file('image');
                $image->storeAs('public/products', $image->hashName());
                $product->image = $image->hashName();
            }

            // Update other product details
            $product->update($request->only('name', 'description', 'price'));

            return response()->json([
                'product' => $product,
                'message' => 'Product updated successfully',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }
}
