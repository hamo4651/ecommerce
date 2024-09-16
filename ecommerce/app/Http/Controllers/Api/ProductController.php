<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "description" => "required",
            "image" => "image|mimes:jpeg,png,jpg",
            "price" => "required",
            "stock" => "required",
            "status" => "required",
            "category_id" => "required"
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images/product'), $imageName);
            $validatedData['image'] = asset('images/product/' . $imageName);
        }

        $product = Product::create($validatedData);
        return new ProductResource($product);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "description" => "required",
            "image" => "image|mimes:jpeg,png,jpg",
            "price" => "required",
            "stock" => "required",
            "status" => "required",
            "category_id" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $imageName = null;
        if ($request->hasFile('image')) {
            if ($product->image) {
                $oldImagePath = str_replace(asset('images/product/'), '', $product->image);
                $oldImagePath = public_path('images/product/' . $oldImagePath);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }



                $image = $request->file('image');
                $imageName = time() . '.' . $image->extension();
                $image->move(public_path('images/product'), $imageName);
                $validatedData['image'] = asset('images/product/' . $imageName);
            }
            $product->update($validatedData);
            return new ProductResource($product);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        if ($product->image) {
            $imagePath = str_replace(asset('images/product/'), '', $product->image);
            $imagePath = public_path('images/product/' . $imagePath);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
