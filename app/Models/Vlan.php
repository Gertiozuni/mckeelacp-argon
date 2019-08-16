<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vlan extends Model
{
    protected $fillable = [ 'vlan', 'notes', 'subnet', 'alert' ];
    public $timestamps = false;
}
