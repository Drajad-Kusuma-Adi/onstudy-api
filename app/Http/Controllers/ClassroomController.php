<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\UserClass;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    protected $model = Classes::class;

    protected $validationRules = [
        'create' => [
            'title' => 'required',
            'subject' => 'required|in:Sains,Matematika,Bahasa,Teknologi,Sosial,Seni',
        ],
        'update' => [
            'title' => 'required',
            'subject' => 'required|in:Sains,Matematika,Bahasa,Teknologi,Sosial,Seni'
        ]
    ];
}
