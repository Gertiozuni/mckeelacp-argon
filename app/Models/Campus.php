<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
	protected $fillable = [ 'name', 'abbreviation', 'code' ];
    protected $table = 'campuses'; 
    public $timestamps = false;

    public function switches()
    {
    	return $this->hasMany( NSwitch::class, 'campus_id' );
    }
}
