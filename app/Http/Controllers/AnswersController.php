<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class AnswersController extends Controller
{
    protected $model = Answer::class;
    protected $validation = [
        // Regular CRUD
        'create' => [
            'question_id' => ['required', 'uuid'],
            'answer' => ['required', 'string'],
            'right_answer' => ['required', 'boolean'],
        ],
        'update' => [
            'id' => ['required', 'uuid'],
            'question_id' => ['required', 'uuid'],
            'answer' => ['required', 'string'],
            'right_answer' => ['required', 'boolean'],
        ],
    ];
}
