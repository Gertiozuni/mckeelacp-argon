<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomTeacher extends Model
{
    protected $fillable = [ 'staff_id', 'first_name', 'last_name', 'campus_id' ];
    public $timestamps = false;
}

