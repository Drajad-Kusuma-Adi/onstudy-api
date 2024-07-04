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
                    'question' => $questionData['question']
                ];

                foreach ($questionData['answers'] as $answerData) {
                    $answers[] = [
                        'id' => Str::uuid(),
                        'question_id' => $questionId,
                        'answer' => $answerData['answer'],
                        'right_answer' => $answerData['right_answer']
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
            throw $err;
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
}
