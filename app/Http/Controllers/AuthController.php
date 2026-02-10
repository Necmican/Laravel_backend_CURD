<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;

class AuthController extends Controller
{
    // 1. KAYIT OL (Register)
    public function register(Request $request)
    {
        // Kurallar
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kullanıcı Oluşturma
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password) // Şifreyi gizle
        ]);

        // Token (Anahtar) Ver
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Kayıt başarılı',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    // 2. GİRİŞ YAP (Login)
    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Giriş bilgileri hatalı' 
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        
        // Yeni Token Ver
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Giriş başarılı',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    // ÇIKIŞ YAP 
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Tüm cihazlardan çıkış yapıldı.'
        ], 200);
    }

    public function index()
    {
        
        $users =User::all();

    
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                
                'is_logged_in' => $user->tokens()->exists(),
                
                'created_at' => $user->created_at,
            ];
        });

        return response()->json([
            'message' => 'Kullanıcı listesi ve durumları',
            'data' => $data
        ], 200);
    }


}