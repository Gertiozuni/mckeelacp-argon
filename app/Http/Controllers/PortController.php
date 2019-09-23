<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\Port;

use App\Helpers\SwitchHelper;

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

    /*
    *	update the ports mode
    */
    public function updateMode( Port $port ) 
    {
        $helper = new SwitchHelper;
        $helper->changeMode($port);

        $port->load( 'vlans' );

        return response()->json([
            'port' => $port
        ]);
    }

    /*
    *	update the ports vlans
    */
    public function updateVlans( Port $port ) 
    {
        $helper = new SwitchHelper;
        $helper->changeVlans($port);

        $port->load( 'vlans' );

        return response()->json([
            'port' => $port
        ]);
    }
}
