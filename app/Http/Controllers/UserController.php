<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // TODO: There's no point in separating this, should put thes inline instead. But I don't want to fix what's not really broken, yet.
    private array $validation = [
        'register' => [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255'],
        ],
        'login' => [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ],
        'verify' => [
            'remember_token' => ['required', 'string', 'max:255'],
        ],
        'logout' => [
            'remember_token' => ['required', 'string', 'max:255'],
        ],
        'update_profile' => [
            'id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:255'],
        ],
    ];

    public function login(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['login']);

        // Check if the user exists
        $user = User::where('email', $data['email'])->first();

        if ($user) {
            // Validate login data
            if (Hash::check($data['password'], $user->password)) {
                $token = bin2hex(random_bytes(16));
                $user->remember_token = $token;
                $user->save();
                return response()->json($user);
            } else {
                return response()->json(['message' => "Password salah"], 401);
                return response()->json(['message' => "Password salah"], 401);
            }
        } else {
            return response()->json(['message' => "Email tidak ditemukan"], 404);
        }
    }

    public function register(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['register']);

        // Manually append id and hash password
        $data['id'] = Str::uuid();
        $data['password'] = Hash::make($data['password']);

        DB::beginTransaction();
        try {
            $user = User::create($data);
            DB::commit();
            $user->remember_token = bin2hex(random_bytes(16));
            $user->save();
            return response()->json($user); // Success response
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['verify']);
        $user = User::where('remember_token', $data['remember_token'])->first();

        // Check and respond based on the validity of the remember_token
        return $user ? response()->json(['message' => 'Terverifikasi']) : response()->json(['message' => 'Token salah'], 401);
    }

    public function logout(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['logout']);
        $user = User::where('remember_token', $data['remember_token'])->first();

        if ($user) {
            // Update remember_token to null for logout
            $user->remember_token = null;
            $user->save();
            return response()->json(['message' => 'Logged out']);
        } else {
            return response()->json(['message' => 'Token salah'], 401);
        }
    }

    public function update_profile(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['update_profile']);

        // Upload photo if it's included in the request
        if ($req->hasFile('photo')) {
            $filename = $this->uploadFile($req, 'photo');

            $user = User::findOrFail($data['id']);
            $oldPhoto = $user->photo;

            // Delete old photo if it exists
            if (!empty($oldPhoto)) {
                $this->deleteFile($oldPhoto);
            }

            // Update the user's photo
            DB::beginTransaction();
            try {
                $user->update(['photo' => $filename]);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

        // Update user name if it's different from the old one
        $user = User::findOrFail($data['id']);
        if ($user->name !== $data['name']) {
            DB::beginTransaction();
            try {
                $user->update(['name' => $data['name']]);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

        return response()->json($user);
    }
}
