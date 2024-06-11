<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = [];

    // one-to-many with assignments
    public function assignments() {
        return $this->hasMany(Assignment::class, 'classroom_id', 'id');
    }
}
