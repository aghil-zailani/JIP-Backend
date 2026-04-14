<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\InformasiInstansi;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }        

        $user = auth()->guard('api')->user();

        if ($user->role !== 'inspektor') {
            
            auth()->guard('api')->logout(); 

            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Admin yang diperbolehkan masuk melalui API ini.'
            ], 403);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => auth()->guard('api')->user(), 
            'authorization' => [
                'access_token' => $token,
                'type' => 'bearer',
                'expires_in' => auth()->guard('api')->factory()->getTTL() * 60 //GANTI INI JADI 24 JAM (60 GANTI JADI 1440)
            ]
        ]);
    }

    public function register(Request $request)
    {       
        $validator = Validator::make($request->all(), [
            
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:inspektor',
            'no_hp' => 'nullable|string',            
            'nama_instansi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'logo_instansi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();

        try {            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'no_hp' => $request->no_hp,
            ]);
       
            $logoPath = null;
            if ($request->hasFile('logo_instansi')) {
                
                $file = $request->file('logo_instansi');
                $filename = time() . '_' . $file->getClientOriginalName();
                $logoPath = $file->storeAs('logos', $filename, 'public');
            }
            
            $instansi = InformasiInstansi::create([
                'user_id' => $user->id, 
                'nama_instansi' => $request->nama_instansi,
                'alamat' => $request->alamat,
                'logo_instansi' => $logoPath,
            ]);
    
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User dan Instansi berhasil didaftarkan',
                'data' => [
                    'user' => $user,
                    'instansi' => $instansi
                ]
            ], 201);

        } catch (\Exception $e) {
            
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $users = User::all();
        return response()->json([
            'message' => 'Daftar semua pengguna',
            'data' => $users
        ]);
    }

    public function logout()
    {        
        auth()->guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout'
        ], 200);
    }
}