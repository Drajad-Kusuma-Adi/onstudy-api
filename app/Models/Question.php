<?php

namespace App\Models;

class Question extends BaseModel
{
    // one-to-many with answers
    public function answers() {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
}
