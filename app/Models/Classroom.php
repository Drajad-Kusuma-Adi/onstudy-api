<?php

namespace App\Models;

class Classroom extends BaseModel
{
    // one-to-many with assignments
    public function assignments() {
        return $this->hasMany(Assignment::class, 'classroom_id', 'id');
    }
}
