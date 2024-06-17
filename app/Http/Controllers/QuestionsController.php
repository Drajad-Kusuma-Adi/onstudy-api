<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    protected $model = Question::class;
    protected $validation = [
        'create' => [
            'assignment_id' => ['required', 'uuid'],
            'question' => ['required', 'string'],
            'number' => ['required', 'integer'],
        ],
        'update' => [
            'id' => ['required', 'uuid'],
            'assignment_id' => ['required', 'uuid'],
            'question' => ['required', 'string'],
            'number' => ['required', 'integer'],
        ],
    ];
}
