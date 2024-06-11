<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $guarded = [];

    // many-to-many with classrooms
    public function classrooms() {
        return $this->belongsToMany(Classroom::class, 'user_classrooms', 'user_id', 'classroom_id');
    }
    // many-to-many with assignments
    public function assignments() {
        return $this->belongsToMany(Assignment::class, 'submissions', 'user_id', 'assignment_id');
    }
}
