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
        ]
    ];

    public function register(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['register'], ['name', 'email', 'password']);
        $data['password'] = bcrypt($data['password']);
        $user = $this->create($data);
        return $this->jsonResponse($user);
    }
    public function login(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['login'], ['email', 'password']);
        $user = $this->model::where('email', $data['email'])->first();
        if ($user && Hash::check($data['password'], $user->password)) {
            $token = bin2hex(random_bytes(16));
            $user->remember_token = $token;
            $user->save();
            return $this->jsonResponse($user);
        } else {
            return $this->jsonResponse(['message' => 'Invalid credentials'], 401);
        }
    }
    public function verify(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['verify|logout'], ['remember_token']);
        $user = $this->model::where('remember_token', $data['remember_token'])->first();
        return $user ? $this->jsonResponse(['message' => 'Verified']) : $this->jsonResponse(['message' => 'Invalid token'], 401);
    }
    public function logout(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['verify|logout'], ['remember_token']);
        $user = $this->model::where('remember_token', $data['remember_token'])->first();
        $user->remember_token = null;
        $user->save();
        return $user ? $this->jsonResponse(['message' => 'Logged out']) : $this->jsonResponse(['message' => 'Invalid token'], 401);
    }
}
