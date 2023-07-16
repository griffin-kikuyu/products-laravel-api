<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|',
            'description' => 'required|min:6',
            'price' => 'required|max:6',
        ]);
    
        $product = product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
        ]);
    
        return response()->json(['message' => 'product created successful.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
            // Find the product by ID
            $product = Product::findOrFail($id);
    
            // Return a JSON response with the product data
            return response()->json($product);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        // Update the product attributes
        $product->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        // Return a JSON response with a success message or updated data
        return response()->json(['message' => 'Product updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
                // Find the product by ID
                $product = Product::findOrFail($id);

                // Delete the product
                $product->delete();
        
                // Return a JSON response with a success message
                return response()->json(['message' => 'Product deleted successfully.']);
    }
}
