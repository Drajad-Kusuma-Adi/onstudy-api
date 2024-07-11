<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Assignment;
use App\Models\Question;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignmentsController extends Controller
{
    private $validation = [
        'create_full_assignment' => [
            'classroom_id' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'deadline' => ['required', 'date'],
            'questions' => ['required', 'array'],
        ]
    ];

    public function create_full_assignment(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['create_full_assignment']);

        if ($data['deadline'] < date('Y-m-d')) {
            return response()->json(['message' => 'Deadline cannot be in the past'], 400);
        }

        DB::beginTransaction();
        try {
            // Create assignment
            $assignment = $this->createAssignment($data);

            // Prepare arrays for batch insertions
            $questions = [];
            $answers = [];

            // Collect questions and answers for batch insertion
            foreach ($data['questions'] as $questionData) {
                $questionId = Str::uuid();
                $questions[] = [
                    'id' => $questionId,
                    'assignment_id' => $assignment->id,
                    'question' => $questionData['question'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($questionData['answers'] as $answerData) {
                    $answers[] = [
                        'id' => Str::uuid(),
                        'question_id' => $questionId,
                        'answer' => $answerData['answer'],
                        'right_answer' => $answerData['right_answer'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Batch insert questions
            Question::insert($questions);

            // Batch insert answers
            Answer::insert($answers);

            // Commit the transaction
            DB::commit();
        } catch (Throwable $err) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return response()->json(['message' => $err->getMessage()], 500);
        }

        return response()->json($assignment);
    }
    // Helper method for create_full_assignment method
    private function createAssignment($data)
    {
        return Assignment::create([
            'id' => Str::uuid(),
            'classroom_id' => $data['classroom_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline']
        ]);
    }

    public function get_full_assignments_by_classroom_id(string $classroom_id) {
        try {
            $assignments = Assignment::where('classroom_id', $classroom_id)->get()->toArray();

            foreach ($assignments as &$assignment) {
                $assignment['questions'] = [];

                $questions = Question::where('assignment_id', $assignment['id'])->get()->toArray();
                foreach ($questions as &$question) {
                    $question['answers'] = [];

                    $answers = Answer::where('question_id', $question['id'])->get()->toArray();
                    foreach ($answers as $answer) {
                        $question['answers'][] = $answer;
                    }
                    $assignment['questions'][] = $question;
                }
            }

            // Break the reference with the last element to potential bugs in case I modify this code again
            unset($assignment);
            unset($questions);
            return response()->json($assignments);
        } catch (Throwable $err) {
            return response()->json(['message' => $err->getMessage()], 500);
        }
    }

    // public function get_assignment_by_id(string $assignment_id) {
    //     return response()->json(Assignment::find($assignment_id));
    // }

    // public function update_assignment(Request $req) {

    // }

    // public function delete_assignment(string $assignment_id) {
    //     return response()->json(Assignment::destroy($assignment_id));
    // }
}
