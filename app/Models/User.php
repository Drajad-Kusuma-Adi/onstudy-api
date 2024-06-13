<?php

namespace App\Models;

class User extends BaseModel
{
    protected $hidden = [
        'password'
    ];

    // many-to-many with classrooms
    public function classrooms() {
        return $this->belongsToMany(Classroom::class, 'user_classrooms', 'user_id', 'classroom_id');
    }
    // many-to-many with assignments
    public function assignments() {
        return $this->belongsToMany(Assignment::class, 'submissions', 'user_id', 'assignment_id');
    }
}
