<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use ErrorException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use function Laravel\Prompts\error;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      return CategoryResource::collection(Category::all());
    }

     public function getproductcategory($id){
         $category = Category::find($id);
         if (!$category) {
             return response()->json([
                 'message' => 'Category not found'
             ], 404);
         } 
         $products = $category->products;
         return ProductResource::collection($products);
     }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' =>['required','unique:categories','min:3'], 
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|nullable'
        ];
        $validator =validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
         $validatedData = $validator->validated(); 
        $imageName=null;
        if($request->hasFile('image')){
            $image = $request->file('image');
           $imageName = time().'.'.$image->extension();
            $image->move(public_path('images/category'),$imageName);
            $validatedData['image'] = asset('images/category/' . $imageName);
        } 
        
            $category = Category::create($validatedData);
            return new CategoryResource($category);

       
    }

  
    public function show($id)
    {
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }
        $rules = [
            'name' =>['required','min:3',
            Rule::unique(table: "categories")->ignore($category->id),

        ], 
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|nullable'
        ];
        $validator =validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $validatedData = $validator->validated();
        
        
        $imageName=null;
        if($request->hasFile('image')){
            $image = $request->image;   
            $imageName = time().'.'.$image->extension();
            $image->move(public_path('images/category'),$imageName);
            $validatedData['image'] = asset('images/category/' . $imageName);
              
        } 
        $category->update($validatedData);
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
        
    }
}
