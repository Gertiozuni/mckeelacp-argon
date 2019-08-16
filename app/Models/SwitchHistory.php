<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwitchHistory extends Model
{
	protected $fillable = [];
    protected $table = 'switch_history'; 
    public $timestamps = false;
    protected $dates = [
        'created_at'
    ];

    public function user() 
    {
    	return $this->belongsTo( User::class, 'user_id', 'id' );
    }
}
