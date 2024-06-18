<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Models\UserClassroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    protected $model = Classroom::class;
    protected $validation = [
        // Regular CRUD
        'create' => [
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'in:Sains,Matematika,Bahasa,Informatika,Sosial,Seni'],
        ],
        'update' => [
            'id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'in:Sains,Matematika,Bahasa,Informatika,Sosial,Seni'],
        ],

        // Specific endpoints
        'find_teacher' => [
            'id' => ['required', 'uuid'],
        ]
    ];

    public function find_teacher(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['find_teacher']);
        $teacher = UserClassroom::where('classroom_id', $data['id'])->where('role', 'Teacher')->first();
        $teacher = User::find($teacher->user_id);
        return $this->jsonResponse($teacher);
    }
}
