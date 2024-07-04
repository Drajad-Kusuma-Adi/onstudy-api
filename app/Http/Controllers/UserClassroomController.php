<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    ];

    public function read_user_classroom_by_user_id(Request $req) {
        $data = $this->validateRequest($req, ['id' => ['required', 'uuid']]);
        $classroom = $this->readByColumn('user_id', $data['id']);
        return $this->jsonResponse($classroom);
    }

    public function read_user_classroom_by_classroom_id(Request $req)
    {
        $data = $this->validateRequest($req, ['id' => ['required', 'uuid']]);
        $classroom = $this->readByColumn('classroom_id', $data['id']);
        return $this->jsonResponse($classroom);
    }

    public function get_teacher(Request $req)
    {
        $data = $this->validateRequest($req, ['id' => ['required', 'uuid']]);
        $classroom = UserClassroom::where('user_id', $data['id'])->where('role', 'Teacher')->first();
        $teacher = User::find($classroom['user_id']);
        return $this->jsonResponse($teacher);
    }
}
