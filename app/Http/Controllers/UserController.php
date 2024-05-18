<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $model = User::class;

    protected $validationRules = [
        'create' => [
            'username' => 'required',
            'institution' => 'required'
        ],
        'update' => [
            'username' => 'required',
            'institution' => 'required'
        ]
    ];
}
