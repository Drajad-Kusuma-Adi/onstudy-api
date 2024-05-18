<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    protected $model = Material::class;

    protected $validationRules = [
        'create' => [
            'class_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'type' => 'required|in:assignment,material',
        ],
        'update' => [
            'class_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'type' => 'required|in:assignment,material',
        ]
    ];
}
