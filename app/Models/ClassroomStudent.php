<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomStudent extends Model
{
    protected $fillable = [ 'student_id', 'first_name', 'last_name', 'campus_id', 'grade' ];
    public $timestamps = false;
}
