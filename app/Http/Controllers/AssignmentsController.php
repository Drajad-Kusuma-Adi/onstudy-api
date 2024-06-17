<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentsController extends Controller
{
    protected $model = Assignment::class;
    protected $validation = [
        // Regular CRUD
        'create' => [
            'classroom_id' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'deadline' => ['required', 'date'],
        ],
        'update' => [
            'id' => ['required', 'uuid'],
            'classroom_id' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'deadline' => ['required', 'date'],
        ],
    ];
}
