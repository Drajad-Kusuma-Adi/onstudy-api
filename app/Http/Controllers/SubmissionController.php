<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    protected $model = Submission::class;

    protected $validationRules = [
        'create' => [
            'user_id' => 'required',
            'material_id' => 'required',
        ],
        'update' => []
    ];
}
