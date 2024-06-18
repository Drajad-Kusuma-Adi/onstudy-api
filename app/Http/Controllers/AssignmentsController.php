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

        // Specific endpoints
        'read_by_classroom_id' => [
            'id' => ['required', 'uuid'],
        ]
    ];

    public function read_by_classroom_id(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['read_by_classroom_id']);
        $assignments = $this->readByColumn('classroom_id', $data['id']);
        return $this->jsonResponse($assignments);
    }
}
