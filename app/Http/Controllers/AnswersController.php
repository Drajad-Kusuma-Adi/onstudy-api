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

        // Specific endpoints
        'read_by_question_id' => [
            'id' => ['required', 'uuid'],
        ]
    ];

    public function read_by_question_id(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['read_by_question_id']);
        $answers = $this->readByColumn('question_id', $data['id']);
        return $this->jsonResponse($answers);
    }
}
