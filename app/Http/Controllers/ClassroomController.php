<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    protected $model = Classroom::class;
    protected $validation = [
        // Regular CRUD
        'create' => [
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'in:Sains,Matematika,Bahasa,Informatika,Sosial,Seni'],
        ],
        'update' => [
            'id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'in:Sains,Matematika,Bahasa,Informatika,Sosial,Seni'],
        ],
    ];
}
