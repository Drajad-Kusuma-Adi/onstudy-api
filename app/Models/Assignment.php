<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // one-to-many with questions
    public function questions() {
        return $this->hasMany(Question::class, 'assignment_id', 'id');
    }
}
