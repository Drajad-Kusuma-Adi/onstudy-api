<?php

namespace App\Models;

class Assignment extends BaseModel
{
    // one-to-many with questions
    public function questions() {
        return $this->hasMany(Question::class, 'assignment_id', 'id');
    }
}
