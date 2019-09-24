<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
	protected $fillable = [ 'port', 'description', 'active' ];
    protected $table = 'ports'; 
    public $timestamps = false;
    protected $dates = [
        'last_updated',
        'checked_in'
    ];

    public function vlans() 
    {
    	return $this->belongsToMany( Vlan::class );
    }

    public function switch()
    {
     	return $this->belongsTo( NSwitch::class, 'switch_id', 'id' );
    }

    public function history() 
    {
        return $this->hasMany( NetworkPortHistory::class, 'port_id', 'id' );
    }
}
