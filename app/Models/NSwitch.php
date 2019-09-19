<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NSwitch extends Model
{
    protected $fillable = [ 'ip_address', 'mac_address', 'fiber_ports', 'location', 'sub_location', 'campus_id', 'model', 'uptime', 'checked_in', 'active' ];
    protected $table = 'switches'; 
    public $timestamps = false;
    protected $dates = [
        'uptime',
        'checked_in'
    ];

    public function ports() 
    {
    	return $this->hasMany( Port::class, 'switch_id' );
    }

    public function campus() 
    {
    	return $this->belongsTo( Campus::class, 'campus_id', 'id' );
    }

    public function history() 
    {
        return $this->hasMany( SwitchHistory::class, 'switch_id', 'id' );
    }
}
