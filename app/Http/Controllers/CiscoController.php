<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

class CiscoController extends Controller
{
    /*
    *	Show search page
    */
    public function searchIndex() 
    {
    	return view( 'cisco.search' );
    }

    /*
    *	Search action
    */
    public function search( Request $request )
    {
    	/* init */
        $key = config( 'mckeel.ciscokey' );
        $serials = $request->serials;
        $serials = preg_split( '/,|\n|\r\n?/', $serials );
        
        $networks = $this->getNetworks( $key );

        $client = new Client( ['base_uri' => 'https://n79.meraki.com/api/v0/'] );

        $results = [];

        /* now lets loop through all the network and find them */
        foreach( $networks as $name => $id )
        {

            if( ! count( $serials ) )
            {
                break;
            }

            foreach( $serials as $k => $serial )
            {
                /* first search by serial */
                $res = $client->request( 'GET', 'networks/' . $id . '/sm/devices', [
                    'headers'   => [
                        'X-Cisco-Meraki-API-Key' => $key
                    ],
                    'form_params' => [ 'serials' => $serial ],
                    'http_errors' => false
                ] );

                $body = json_decode( $res->getBody() );

                if( count( $body->devices ) > 0 ) 
                {
                    $body->devices[ 0 ]->network = $name;
                    $results[] = $body->devices[ 0 ];

                    unset( $serials[ $k ] );
                
                }

                if( ! count( $serials ) )
                {
                    break;
                }

                /* now search by mac */
                $res = $client->request( 'GET', 'networks/' . $id . '/sm/devices', [
                    'headers'   => [
                        'X-Cisco-Meraki-API-Key' => $key
                    ],
                    'form_params' => [ 'wifiMacs' => $serial ],
                    'http_errors' => false
                ] );

                $body = json_decode( $res->getBody() );

                if( isset( $body->devices ) && count( $body->devices ) > 0 ) 
                {

                    $body->devices[ 0 ]->network = $name;
                    $results[] = $body->devices[ 0 ];

                    unset( $serials[ $k ] );
                
                }

            }
            sleep( 1 );              

        }

        return response()->json( [ 'devices' => $results ] );
    }

    /*
    *   Show wipe page
    */
    public function wipeIndex() 
    {
        return view( 'cisco.wipe' );
    }

    /*
    *   wipe action
    */
    public function wipe( Request $request ) 
    {
        /* init */
        $key = config( 'mckeel.ciscokey' );
        $serials = $request->serials;
        $serials = preg_split( '/,|\n|\r\n?/', $serials );

        $networks = $this->getNetworks( $key );
        $client = new Client( ['base_uri' => 'https://api.meraki.com/api/v0/'] );

        /* now lets loop through all the network and serials to wipe them */
        foreach( $networks as $name => $id )
        {

            if( ! count( $serials ) )
            {
                break;
            }

            foreach( $serials as $k => $serial )
            {
                $res = $client->request( 'PUT', 'networks/' . $id . '/sm/device/wipe', [
                    'headers'   => [
                        'X-Cisco-Meraki-API-Key' => $key
                    ],
                    'form_params' => [ 'serial' => trim( $serial ) ],
                    'http_errors' => false
                ] );

                if( ! in_array( $res->getStatusCode(), [ 400, 404 ] ) )
                {
                    unset( $serials[ $k ] );
                }
            }

        }

        if( ! count( $serials ) )
        {
            flash( 'iPads have been wiped' );
        }
        else
        {
            $message = 'The Following iPads could not be found: <br />' ;
            
            foreach( $serials as $serial )
            {
                $message .= $serial . '<br />';
            }

            flash( $message )->error();

        }

        flash( 'iPads have been successfully wiped' )->success();
        return back();  
    }

    /*
    *	helper function to return the orgizeations in cisco mdm
    */
    private function getNetworks( $key )
    {
        $orgId = config( 'mckeel.ciscoid' );

        $client = new Client( ['base_uri' => 'https://n79.meraki.com/api/v0/'] );

        /* First lets get all the network ids */
        $networks = [];
        $res = $client->request( 'GET', 'organizations/' . $orgId . '/networks', [
            'headers'   => [
                'X-Cisco-Meraki-API-Key' => $key
            ]
        ] );

        $body = $res->getBody();
        $data = json_decode( $body->getContents() );

        foreach( $data as $network )
        {
            /* we want all of it except transport */
            if( $network->name != 'Transportation' )
            {
                $networks[ $network->name ] = $network->id;
            }
        }

        return $networks;
    }
}
