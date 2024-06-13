<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $model = User::class;
    protected $validation = [
        'register' => [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255'],
        ],
        'login' => [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ],
        'verify|logout' => [
            'remember_token' => ['required', 'string', 'max:255'],
        ],
    ];

    public function auth(Request $req)
    {
        // Check if the user exists
        $user = $this->model::where('email', $req->input('email'))->first();

        if ($user) {
            // If user exists, validate login data
            return $this->jsonResponse($this->login($req, $user));
        } else {
            // If user does not exist, validate registration data and register the user
            $this->register($req);

            // Login the user after registration
            $user = $this->model::where('email', $req->input('email'))->first();
            return $this->jsonResponse($this->login($req, $user));
        }
    }

    private function login($req, $user)
    {
        $data = $this->validateRequest($req, $this->validation['login'], ['email', 'password']);

        // Generate and assign a remember_token for authentication
        if (Hash::check($data['password'], $user->password)) {
            $token = bin2hex(random_bytes(16));
            $user->remember_token = $token;
            $user->save();
            return $user;
        } else {
            return $this->jsonResponse(['message' => $user->password.' '.$data['password']], 401);
        }
    }

    private function register($req)
    {
        $data = $this->validateRequest($req, $this->validation['register'], ['name', 'email', 'password']);
        $initialPassword = $data['password'];
        $data['password'] = bcrypt($data['password']);
        $this->create($data);
        $data['password'] = $initialPassword;
    }

    public function verify(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['verify|logout'], ['remember_token']);
        $user = $this->model::where('remember_token', $data['remember_token'])->first();
        // Check and respond based on the validity of the remember_token
        return $user ? $this->jsonResponse(['message' => 'Verified']) : $this->jsonResponse(['message' => 'Invalid token'], 401);
    }

    public function logout(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['verify|logout'], ['remember_token']);
        $user = $this->model::where('remember_token', $data['remember_token'])->first();
        // Update remember_token to null for logout
        $user->remember_token = null;
        $user->save();
        return $user ? $this->jsonResponse(['message' => 'Logged out']) : $this->jsonResponse(['message' => 'Invalid token'], 401);
    }
}
