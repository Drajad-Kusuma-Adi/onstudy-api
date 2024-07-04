<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Models\UserClassroom;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function create_classroom(Request $req) {
        $data = $this->validateRequest($req, [
            "name" => ["required", "string", "max:255"],
            "subject" => ["required", "string", "in:Sains,Matematika,Bahasa,Informatika,Sosial,Seni"],
            "user_id" => ["required", "uuid"],
        ]);
        $data['id'] = Str::uuid();

        // Store the user_id before removing it from $data
        $userId = $data['user_id'];
        unset($data['user_id']);

        DB::beginTransaction();
        try {
            $classroom = Classroom::create($data);
            UserClassroom::create([
                "id" => Str::uuid(),
                "classroom_id" => $data['id'],
                "user_id" => $userId,
                "role" => "Teacher",
            ]);
            $classroom["teacher"] = User::find($userId);
            DB::commit();
            return response()->json($classroom);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    // public function update_classroom(Request $req) {

    // }

    public function join_classroom(Request $req) {
        $data = $this->validateRequest($req, [
            "classroom_id" => ["required", "uuid"],
            "user_id" => ["required", "uuid"],
        ]);
        $data['id'] = Str::uuid();
        $data['role'] = "Student";

        DB::beginTransaction();
        try {
            UserClassroom::create($data);
            $classroom = Classroom::find($data['classroom_id']);
            $classroom["teacher"] = User::find($data['user_id']);

            if ($data['user_id'] === $classroom['teacher']['id']) {
                throw new \Exception("Tidak bisa bergabung dengan kelas yang anda ajar.");
            }

            if (UserClassroom::where("classroom_id", $data['classroom_id'])->where("user_id", $data['user_id'])->exists()) {
                throw new \Exception("Anda sudah bergabung dengan kelas ini.");
            }

            DB::commit();
            return response()->json($classroom);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    // public function leave_classroom(Request $req) {

    // }

    public function get_classrooms_by_user_id(Request $req) {
        $data = $this->validateRequest($req, ["id" => ["required", "uuid"]]);
        try {
            $user_classrooms = UserClassroom::where("user_id", $data["id"])->get();
            $classroom_ids = $user_classrooms->pluck('classroom_id')->toArray();
            $classrooms = Classroom::whereIn('id', $classroom_ids)->get();
            foreach ($classrooms as $classroom) {
                $classroom["teacher"] = User::find(UserClassroom::where("classroom_id", $classroom->id)->where("role", "Teacher")->first()->user_id);
            }
            return response()->json($classrooms);
        } catch (Throwable $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
