<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwitchLog extends Model
{
	protected $guarded = [ 'id' ];
    protected $table = 'switch_logs'; 
    public $timestamps = false;
    protected $dates = [
        'created_at'
    ];

    public function user() 
    {
    	return $this->belongsTo( User::class, 'user_id', 'id' );
    }

    public function switch() 
    {
    	return $this->belongsTo( NSwitch::class, 'switch_id', 'id' );
    }

    public function port() 
    {
    	return $this->belongsTo( Port::class, 'port_id', 'id' );
    }
}
