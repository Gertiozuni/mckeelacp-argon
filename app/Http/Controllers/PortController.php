<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\Port;

use App\Helpers\SwitchHelper;

use Carbon\Carbon;

class PortController extends Controller
{

    /*
    *	view the documented port logs
    */
    public function logs( Port $port ) 
    {

        $search = request()->search;

        // get the logs
        $logs = $port->logs()
            ->orderByDesc( 'created_at' )
            ->when( $search, function( $query ) use ( $search ) {
                return $query->where( 'event', 'LIKE', '%' . $search . '%' );
            } )
            ->with( 'user' )
            ->paginate( 20 );

        // if ajex return object
        if( request()->ajax() )
        {
            return response()->json([
                'logs' => $logs
            ]);
        }

        // saftey check
        if( ! $port->logs->count() )
        {
            flash( 'Could not find any logs for this port.' );
            return back();
        }
        
        return view( 'network.ports.logs', compact( 'port', 'logs' ) );
    }

    /*
    *	update the ports description
    */
    public function updateDescription( Port $port ) 
    {

        $description = request()->description;

        /* document the change */
        $port->logs()->create( [
            'switch_id' => $port->switch_id,
            'event' => 'Description has been changed from "' . $port->description . '" to "' . $description . '"',
            'user_id' => auth()->user()->id,
            'created_at' => Carbon::now()
        ]);

        $port->description = $description;
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
