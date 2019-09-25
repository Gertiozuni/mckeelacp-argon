<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\Port;
use App\Models\PortHistory;

use App\Helpers\SwitchHelper;

use Carbon\Carbon;

class PortController extends Controller
{

    /*
    *	view the documented port history
    */
    public function showHistory( Port $port ) 
    {

        $search = request()->search;

        // get the histories
        $histories = $port->history()
            ->orderByDesc( 'created_at' )
            ->when( $search, function( $query ) use ( $search ) {
                return $query->where( 'info', 'LIKE', '%' . $search . '%' );
            } )
            ->with( 'user' )
            ->paginate( 20 );

        // if ajex return object
        if( request()->ajax() )
        {
            return response()->json([
                'histories' => $histories
            ]);
        }

        // saftey check
        if( ! $port->history->count() )
        {
            flash( 'Could not find any history for this port.' );
            return back();
        }
        
        return view( 'network.ports.history', compact( 'port', 'histories' ) );
    }

    /*
    *	update the ports description
    */
    public function updateDescription( Port $port ) 
    {

        $description = request()->description;

        /* document the change */
        $port->history()->create( [
            'info' => 'Description has been changed from "' . $port->description . '" to "' . $description . '"',
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
