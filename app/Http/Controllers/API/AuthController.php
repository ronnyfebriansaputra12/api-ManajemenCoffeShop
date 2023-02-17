<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_user' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|min:8',
            'image' => 'string|max:100|nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'no_hp' => 'string|max:15',
            'posisi' => 'string|max:50',
            'level' => 'required|in:admin,owner,karyawan',
        ],[

        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        if($request->hasFile('image')){
            $image = $request->file('image');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $validate['image'] = $name;

        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('authToken')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Register berhasil',
            'data' => $user,
        ], 200);
        
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:6',
        ],[
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.max' => 'Email maksimal 100 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Login gagal',
                'data' => $validator->errors(),
            ], 400);    
        }

        $credentials = request(['email', 'password']);
        if(!auth()->attempt($credentials)){
            return response()->json([
                'success' => false,
                'message' => 'Login gagal',
                'data' => 'Email atau password salah',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Login gagal',
                'data' => 'Email atau password salah',
            ], 400);
        }

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['nama_user'] = $user->nama_user;
        $success['email'] = $user->email;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => $success,
        ], 200);
    }
}
