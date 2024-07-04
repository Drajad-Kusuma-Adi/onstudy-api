<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserController extends Controller
{
    protected array $validation = [
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
        'delete_user' => [
            'id' => ['required', 'uuid'],
        ],
    ];

    public function login(Request $req): JsonResponse
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
                return response()->json(['message' => "Invalid password"], 401);
            }
        } else {
            return response()->json(['message' => "User does not exist"], 404);
        }
    }

    public function register(Request $req): JsonResponse
    {
        $data = $this->validateRequest($req, $this->validation['register']);
        $data['password'] = Hash::make($data['password']);

        DB::beginTransaction();
        try {
            $user = User::create($data);
            DB::commit();
            return $this->jsonResponse($user);  // Success response
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->jsonResponse(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $req): JsonResponse
    {
        $data = $this->validateRequest($req, $this->validation['verify']);
        $user = User::where('remember_token', $data['remember_token'])->first();

        // Check and respond based on the validity of the remember_token
        return $user ? $this->jsonResponse(['message' => 'Verified']) : $this->jsonResponse(['message' => 'Invalid token'], 401);
    }

    public function logout(Request $req): JsonResponse
    {
        $data = $this->validateRequest($req, $this->validation['logout']);
        $user = User::where('remember_token', $data['remember_token'])->first();

        if ($user) {
            // Update remember_token to null for logout
            $user->remember_token = null;
            $user->save();
            return $this->jsonResponse(['message' => 'Logged out']);
        } else {
            return $this->jsonResponse(['message' => 'Invalid token'], 401);
        }
    }

    public function update_profile(Request $req): JsonResponse
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
                return $this->jsonResponse(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
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
                return $this->jsonResponse(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
            }
        }

        return $this->jsonResponse($user);
    }
}
