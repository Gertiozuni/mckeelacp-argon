<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomFile extends Model
{
    protected $fillable = [ 'path', 'students_removed', 'students_added', 'teachers_added', 'teachers_removed', 'created_at' ];
    public $timestamps = false;
    protected $dates = [
        'created_at',
    ];

    public function user()
    {
    	return $this->belongsTo( User::class, 'user_id', 'id' );
    }
}
