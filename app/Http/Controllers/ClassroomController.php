<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    protected $model = Classroom::class;
    protected $validation = [
        // Regular CRUD
        'create_classroom' => [
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'in:Sains,Matematika,Bahasa,Informatika,Sosial,Seni'],
        ],
        'read_classroom_by_id' => [
            'id' => ['required', 'uuid'],
        ],
        'update_classroom' => [
            'id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'in:Sains,Matematika,Bahasa,Informatika,Sosial,Seni'],
        ],
        'delete_classroom' => [
            'id' => ['required', 'uuid'],
        ],
    ];

    public function create_classroom(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['create_classroom']);
        $classroom = $this->create($data);
        return $this->jsonResponse($classroom);
    }

    public function read_classroom_by_id(Request $req) {
        $data = $this->validateRequest($req, $this->validation['read_classroom_by_id']);
        $classroom = $this->readById($data['id']);
        return $this->jsonResponse($classroom);
    }

    public function update_classroom(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['update_classroom']);
        $classroom = $this->update($data, $data['id']);
        return $this->jsonResponse($classroom);
    }

    public function delete_classroom(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['delete_classroom']);
        $classroom = $this->delete($data['id']);
        return $this->jsonResponse($classroom);
    }
}
