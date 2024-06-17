<?php

namespace App\Http\Controllers;

use App\Models\UserClassroom;
use Illuminate\Http\Request;

class UserClassroomController extends Controller
{
    protected $model = UserClassroom::class;
    protected $validation = [
        // Regular CRUD
        'create' => [
            'user_id' => ['required', 'uuid'],
            'classroom_id' => ['required', 'uuid'],
            'role' => ['required', 'string', 'in:Student,Teacher'],
        ],
        'update' => [
            'id' => ['required', 'uuid'],
            'user_id' => ['required', 'uuid'],
            'classroom_id' => ['required', 'uuid'],
            'role' => ['required', 'string', 'in:Student,Teacher'],
        ],

        // Specific methods
        'read_user_classroom_by_user_id' => [
            'user_id' => ['required', 'uuid'],
        ],
        'read_user_classroom_by_classroom_id' => [
            'classroom_id' => ['required', 'uuid'],
        ],
    ];

    public function read_user_classroom_by_user_id(Request $req) {
        $data = $this->validateRequest($req, $this->validation['read_user_classroom_by_user_id']);
        $classroom = $this->readByColumn('user_id', $data['user_id']);
        return $this->jsonResponse($classroom);
    }

    public function read_user_classroom_by_classroom_id(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['read_user_classroom_by_classroom_id']);
        $classroom = $this->readByColumn('classroom_id', $data['classroom_id']);
        return $this->jsonResponse($classroom);
    }
}
