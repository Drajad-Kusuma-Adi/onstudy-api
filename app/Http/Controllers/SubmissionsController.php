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
