<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|min:6',
      'role' => Rule::in(['admin', 'user']),
      'image' => 'image|mimes:jpeg,png,jpg'
    ]);
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
    $user = User::create([
      'name' => $validatedData['name'],
      'email' => $validatedData['email'],
      'role' => $validatedData['role'],
      'image' => $validatedData['image'],
      'password' => Hash::make($validatedData['password']),
    ]);
    $token = $user->createToken('auth_token')->plainTextToken;
    return response()->json([
      'message' => 'User created successfully', 
      'access_token' => $token,
      'token_type' => 'Bearer',
    ]);
  }

  public function login(Request $request){
    try {

      $validator = Validator::make($request->all(), [
        'email' => 'required||email|',
        'password' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'message' => 'Validation failed',
          'errors' => $validator->errors()
        ], 422);
      }
      $user = User::where('email', $request->email)->first();
      if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
          'message' => 'The provided credentials are incorrect.',
        ], 401);
      }
      $token = $user->createToken('auth_token')->plainTextToken;
      return response()->json([
        'message' => 'Login successfully',
        'access_token' => $token,
        'token_type' => 'Bearer',
      ]);


    } catch (\Throwable $th) {
        return response()->json([
            'message' => $th->getMessage()
          ], 500);
    }
  }


  public function profile(){
    $userData = Auth::user(); 
    return response()->json([
        'message' => 'profile information',
        'data' => $userData,
        'id' => Auth::id(),
      ],200);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();
    return response()->json([
      'message' => 'Logout successfully'
    ]);
  }
}
