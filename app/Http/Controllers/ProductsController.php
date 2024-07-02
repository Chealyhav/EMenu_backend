<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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

    /** show a single product */
    public function show(string $id)
    {
        try {
            $product = Product::with('categories')->find($id);

            return response()->json([
                'product' => $product,
                'message' => 'Product retrieved successfully',
            ], 201);
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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'categories.*' => 'exists:categories,id',
            ]);

            $filename = null;
            $path = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $filename = time().'.'.$extension;
                    $path = 'uploads/products/';
                    $file->move($path, $filename);
                } else {
                    throw new \Exception('Uploaded file is not valid');
                }
            }

            $productData = $request->except('image', 'categories');

            if ($filename && $path) {
                $productData['image'] = $path.$filename;
                $productData['image_url'] = asset($path.$filename); // Store full URL if needed
            }

            $product = Product::create($productData);
            $product->categories()->sync($request->categories);

            return response()->json([
                'product' => $product->load('categories'),
                'message' => 'Product created successfully',
                'path' => $path.$filename, // Return path to the image
                'url' => asset($path.$filename), // Return full URL to the image
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }
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
                'des' => 'required|string',
                'price' => 'required|numeric',
                'productCode' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'categories.*' => 'exists:categories,id',
            ]);

            $path = null;
            $filename = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Validate file before moving
                if ($file->isValid()) {
                    // Delete old image if exists
                    if ($product->image && File::exists(public_path('uploads/products/'.$product->image))) {
                        File::delete(public_path('uploads/products/'.$product->image));
                    }

                    // Move new file
                    $extension = $file->getClientOriginalExtension();
                    $filename = time().'.'.$extension;
                    $path = 'uploads/products/';
                    $file->move(public_path($path), $filename);

                    // Update product with new image filename
                    $product->image = $filename;
                } else {
                    throw new \Exception('Uploaded file is not valid');
                }
            }

            // Update other product details
            $product->update($request->except('categories', 'image'));
            $product->categories()->sync($request->input('categories', []));

            return response()->json([
                'product' => $product->load('categories'),
                'path' => $path ? $path.$filename : null, // Return path to the image if updated
                'url' => $path ? asset($path.$filename) : null, // Return full URL to the image if updated
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
        try {
            $product = Product::findOrFail($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }
            if (File::exists($product->image)) {
                // Delete the image file
                File::delete($product->image);

                // File::delete($product->image);
            }

            // Get the image path
            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
