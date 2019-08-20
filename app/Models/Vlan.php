<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vlan extends Model
{
    protected $fillable = [ 'vlan', 'description', 'subnet', 'alert' ];
    public $timestamps = false;
}
