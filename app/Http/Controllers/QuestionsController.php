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

        // Specific endpoints
        'read_by_assignment_id' => [
            'id' => ['required', 'uuid'],
        ]
    ];

    public function read_by_assignment_id(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['read_by_assignment_id']);
        $questions = $this->readByColumn('assignment_id', $data['id']);
        return $this->jsonResponse($questions);
    }
}
