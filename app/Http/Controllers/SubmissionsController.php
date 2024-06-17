<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionsController extends Controller
{
    protected $model = Submission::class;
    protected $validation = [
        'create' => [
            'user_id' => ['required', 'uuid'],
            'assignment_id' => ['required', 'uuid'],
            'grade' => ['required', 'integer'],
        ],
        'update' => [
            'id' => ['required', 'uuid'],
            'user_id' => ['required', 'uuid'],
            'assignment_id' => ['required', 'uuid'],
            'grade' => ['required', 'integer'],
        ],
    ];
}
