<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Port;

class PortController extends Controller
{
    /*
    *	update the ports description
    */
    public function updateDescription( Port $port ) 
    {
        $port->description = request()->description;
        $port->save();

        return response()->json([
            'success' => 'success'
        ]);
    }
}
