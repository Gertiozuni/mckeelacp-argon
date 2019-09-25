<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortHistory extends Model
{
	protected $fillable = [ 'info', 'user_id', 'created_at' ];
    protected $table = 'port_history'; 
    public $timestamps = false;
    protected $dates = [
        'created_at'
    ];

    public function ports() 
    {
    	return $this->belongsTo( Port::class, 'port_id', 'id' );
    }

    public function user() 
    {
    	return $this->belongsTo( User::class, 'user_id', 'id' );
    }

}
