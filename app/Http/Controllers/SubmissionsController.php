<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Assignment;
use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmissionsController extends Controller
{
    public function create_submission(Request $req) {
        try {
            $data = $this->validateRequest($req, [
                'assignment_id' => ['required', 'uuid'],
                'user_id' => ['required', 'uuid'],
                'choices' => ['required', 'array'],
            ]);

            $choices = $data['choices'];
            unset($data['choices']);

            $assignment = Assignment::find($data['assignment_id']);
            if (!$assignment) {
                throw new \Exception('Assignment not found');
            }

            $questions = Question::where('assignment_id', $assignment->id)->get()->toArray();
            foreach ($questions as &$question) {
                $question['right_answer'] = Answer::where('question_id', $question['id'])->where('right_answer', true)->first();
            }
            unset($question);

            $grade = 0;
            $score_per_question = 100 / count($questions);
            foreach ($choices as $choice) {
                $question = collect($questions)->firstWhere('id', $choice['question_id']);
                if (!$question) {
                    throw new \Exception('Question not found');
                }

                if ($question['right_answer']->id === $choice['answer_id']) {
                    $grade += $score_per_question;
                }
            }

            $data['id'] = Str::uuid();
            $data['grade'] = $grade;

            DB::beginTransaction();
            $submission = Submission::create($data);
            DB::commit();

            return response()->json($submission);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_submitters_by_assignment_id($assignment_id) {
        $submissions = Submission::where('assignment_id', $assignment_id)->get();
        $submitters = [];
        foreach ($submissions as $submission) {
            $user = User::where('id', $submission->user_id)->first();
            $user['grade'] = $submission->grade;
            $submitters[] = $user;
        }
        return response()->json($submitters);
    }

    // public function get_submissions_by_assignment_id($assignment_id) {

    // }

    public function get_submissions_by_user_id_with_status($user_id) {
        try {
            $submissions = Submission::where('user_id', $user_id)->get()->toArray();
            $submissions_with_status = [];
            foreach ($submissions as $submission) {
                $data = $submission;
                $assignment = Assignment::find($submission['assignment_id']);
                if ($submission['created_at'] > $assignment->deadline) {
                    $data['ontime'] = true;
                } else {
                    $data['ontime'] = false;
                }
                $submissions_with_status[] = $data;
            }
            response()->json($submissions_with_status);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function check_submission($assignment_id, $user_id) {
        $submission = Submission::where('assignment_id', $assignment_id)->where('user_id', $user_id)->first();
        return $submission ? response()->json(['grade' => $submission->grade]) : response()->json(['message' => 'No submission found']);
    }

    public function update_submission(Request $request, $id) {

    }

    public function delete_submission($id) {

    }

    public function get_avg_grade_by_user_id($user_id) {
        $submissions = Submission::where('user_id', $user_id)->get()->toArray();
        $total = 0;
        $divider = 0;
        foreach ($submissions as $submission) {
            $total += $submission['grade'];
            $divider++;
        }

        if ($divider === 0) {
            return response()->json(['grade' => 0]);
        }

        return response()->json(['grade' => round($total / $divider, 3)]);
    }

}
