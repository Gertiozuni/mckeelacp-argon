<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

use App\Models\Port;
use App\Models\PortVlan;
use App\Models\PortHistory;

use Auth;
use Mail;
use Artisan;

use Carbon\Carbon;

class SwitchHelper {

    private $telnet;
    private $username;
    private $password;

    /*  
    *   Helper function to start the telnet client
    */
    private function startTelnet( $ip )
    {
        $this->username = config( 'mckeel.telnet_id' );
        $this->password = config( 'mckeel.telnet_pass' );
        $this->telnet = @fsockopen( $ip, 23, $errno, $errstr, 5 ); 
    }
    
    /*
    *   Helper to get all the switch data
    */
    public function add( $switch )
    {
        $user = Auth::user();

        /* Init */
        $rawPorts = [];
        $rawFiber = [];
        $rawSysInfo = [];
        $rawConfig = [];
        $ports = [];
        $portCount = 0;
        $model = '';
        $macAddress = '';
        $uptime = '';
        $fiberPorts = [];
        $clear = "\r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n clear \r\n";

        /* Start Telnet Client */
        $this->startTelnet( $switch->ip_address );

        /* Get a listing of ports */
        if( $this->telnet )
        {  
            stream_set_timeout( $this->telnet, 2 );

            /* get ports */
            fputs($this->telnet, "$this->username\r\n");
            fputs($this->telnet, "$this->password\r\n");
            fputs($this->telnet, "enable\r\n");
            fputs($this->telnet, "show interface status\r\n");
            fputs($this->telnet, "\r\n");
            fputs($this->telnet, "\r\n");

            while($line = fgets($this->telnet))
            {
                $rawPorts[] = trim($line);
            }

            /* get system info */
            fputs($this->telnet, $clear );
            fputs($this->telnet, "show system\r\n");


            while($line = fgets($this->telnet))
            {
                $rawSysInfo[] = trim($line);
            }

            /* get config */
            fputs($this->telnet, $clear );
            fputs($this->telnet, "show running-config\r\n");
            fputs($this->telnet, "\r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n" );

            while($line = fgets($this->telnet))
            {
                $rawConfig[] = trim($line);
            }

            /* get fiber ports */
            fputs($this->telnet, $clear );
            fputs($this->telnet, "show fiber-ports optical-transceiver\r\n");
            
            while($line = fgets($this->telnet))
            {
                $rawFiber[] = trim($line);
            }

            fclose( $this->telnet );
        }
        else
        {
            /* Redirect */
            flash( 'Error connecting to ' . $switch->ip_address . ':23' )->error();
            return back();       
        }
        

        /* lets remove the first 9 items we dont even need */
        $rawPorts = array_slice( $rawPorts, 9 );

        /* Get rid of everything we don't need */
        $index = array_search( "console#clear", $rawSysInfo );
        $rawSysInfo = array_slice( $rawSysInfo, $index + 4 );

        /* Get rid of everything we don't need */
        $index = array_search( "console#clear", $rawConfig );
        $rawConfig = array_slice( $rawConfig, $index + 4 );

        /* Get rid of everything we don't need */
        $index = array_search( "console#clear", $rawFiber );
        $rawFiber = array_slice( $rawFiber, $index + 4 );

        $implode = implode( ',', $rawConfig );
        $config = preg_split( "/!/", $implode );

        $x = 1;

        /* lets get the port count, and determine the active ports */
        foreach( $rawPorts as $line )
        {
            /* counts the lines containining 1/g##. Ex 1/g1, 1/g20, 1/g41 */
            if( preg_match( "/[0-9]\/g([1-9]|[1-8][0-9]|9[0-9]|100)/", $line ) )
            {
                $portCount++;

                preg_match("/1\/g(.*?) /", $line, $output );

                $portNum = $output[ 1 ];

                if ( strpos( $line, 'Up') ) 
                {
                    $ports[ $portNum ][ 'active' ] = true;
                }
                else
                {
                    $ports[ $portNum ][ 'active' ] = false;                    
                }

                $ports[ $portNum ][ 'fiber' ] = false;

            }

        }

        /* do we have any fiber ports active */
        foreach( $rawFiber as $line )
        {

            /* counts the lines containining 1/g##. Ex 1/g1, 1/g20, 1/g41 */
            if( preg_match( "/[0-9]\/g([1-9]|[1-8][0-9]|9[0-9]|100)/", $line ) )
            {

                preg_match("/1\/g(.*?) /", $line, $output );

                if( $output[ 1 ] > ( $portCount - $switch->fiber_ports ) )
                {
                    $portNum = $output[ 1 ];
                    $ports[ $portNum ][ 'fiber' ] = true;
                }
            }

        }

        /* finally lets get the system info */
        foreach( $rawSysInfo as $info )
        {

            /* lets look for the mac address */
            if( strpos( $info, 'Address' ) )
            {
                $search = preg_split( "/Burned In MAC Address: /", $info );

                /* Format from switch is 1122.3344.5566, this removes the dots and creates 11:22:33:44:55:66 */
                $macAddress = implode( ":", str_split( str_replace( ".", "", $search[ 1 ] ), 2) ); 

            }

            /* lets look for the model */
            if( strpos( $info, 'Type' ) )
            {
                $output = preg_split("/Machine Type: /", $info );
                $model = $output[ 1 ];
            }

            /* lets look for the uptime */
            if( strpos( $info, 'Time' ) )
            {

                $output = preg_split("/System Up Time: /", $info );
                $data = $output[ 1 ];

                $uptimeData = explode( ' ', $data );
                $timeData = explode( ':', $uptimeData[ 2 ] );

                $days = $uptimeData[ 0 ];
                $hours = preg_replace( "/[^0-9,.]/", "", $timeData[ 0 ] );
                $mins  = preg_replace( "/[^0-9,.]/", "", $timeData[ 1 ] );
                $seconds = preg_replace( "/[^0-9,.]/", "", $timeData[ 2 ] );

                $uptime = Carbon::now();
                $uptime->subDays( $days );
                $uptime->subHours( $hours );
                $uptime->subMinutes( $mins );
                $uptime->subSeconds( $seconds );

            }

        }

        /* run through running config */
        foreach( $config as $line )
        {
            /* counts the lines containining 1/g##. Ex 1/g1, 1/g20, 1/g41 */
            if( preg_match( "/[0-9]\/g([1-9]|[1-8][0-9]|9[0-9]|100)/", $line ) )
            {

                preg_match("/1\/g(.*?),/", $line, $output );
                $portNum = $output[ 1 ];

                if( strpos( $line, 'access' ) ) 
                {
                    preg_match("/access vlan (.*?),/", $line, $vlan);
                    $ports[ $portNum ][ 'mode' ] = 'access';
                    $ports[ $portNum ][ 'vlans' ] = $vlan[ 1 ];

                }
                else if( strpos( $line, 'general' ) )
                {
                    $ports[ $portNum ][ 'mode' ] = 'general';

                    preg_match_all( "/add (.*?) tagged/", $line, $output );
                    $mode = $output[ 1 ];

                    /* get all the vlans */
                    foreach( $output[ 1 ] as $out )
                    {
                        $v = explode( ',', $out );

                        foreach( $v as $set )
                        {
                            if( strpos( $set, '-' ) )
                            {
                                $numbers = explode( '-', $set );
                                $range = range( $numbers[ 0 ], $numbers[ 1 ] );

                                foreach( $range as $r )
                                {
                                    $ports[ $portNum ][ 'vlans' ][] = $r;
                                }
                            }
                            else
                            {
                                $ports[ $portNum ][ 'vlans' ][] = $set;
                            }
                        }
                    }
                }
                else
                {
                    $ports[ $portNum ][ 'mode' ] = null;
                    $ports[ $portNum ][ 'vlans' ] = null;
                }
            }
        }

        /* insert switch */
        $switch->mac_address = $macAddress;
        $switch->model = $model;
        $switch->uptime = $uptime;
        $switch->checked_in = Carbon::now();
        $switch->save();

        /* insert ports */
        $insert = [];
        foreach( $ports as $number => $value )
        {
            $insert[] = [
                'active'        => $value[ 'active' ],
                'port'          => $number, 
                'switch_id'     => $switch->id,
                'mode'          => isset( $value[ 'mode' ] ) ? $value[ 'mode' ] : null,
                'last_updated'  => null,
                'checked_in'    => Carbon::now(),
                'fiber'         => $value[ 'fiber' ]
            ];

        }

        Port::insert( $insert );

        $recentPorts = Port::where( 'switch_id', '=', $switch->id )->get();

        /* insert vlan maps */
        $insert = [];

        foreach( $ports as $number => $value )
        {

            $port = $recentPorts->where( 'port', '=', $number )->first();
            
            if( isset( $value[ 'vlans' ] ) && is_array( $value[ 'vlans' ] ) )
            {
                foreach( $value[ 'vlans' ] as $vlan )
                {
                    $insert[] = [
                        'vlan'       => $vlan,
                        'port_id'    => $port->id
                    ];
                }
            }
            else if( isset( $value[ 'vlans' ] ) )
            {
                $insert[] = [
                    'vlan'       => $value[ 'vlans' ],
                    'port_id'    => $port->id
                ];
            }
        }

        PortVlan::insert( $insert );
    }

    public function canPing( $ip ) 
    {
        exec( "ping -c 1 $ip", $output, $result );

        if( ! $result )
        {
            return false;
        }

        return true;
    }

    /*
    *   change the port mode 
    */
    public function changeMode( $port ) 
    {
        /* init */
        if( isset( request()->vlans[ 'vlan' ] ) ) 
        {
            $vlans = request()->vlans[ 'vlan' ];
        }
        else 
        {
            $vlans = implode( ',', Arr::pluck( request()->vlans, 'vlan' ) );
        }

        $saveConfig = request()->saveConfig;
        $tagged = request()->tagged ? 'tagged' : '';
        $mode = request()->mode;

        /* Start Telnet Client */
        $this->startTelnet( $port->switch->ip_address );

        if( $this->telnet )
        {  
            stream_set_timeout( $this->telnet, 2 );

            /* get ports */
            fputs($this->telnet, "$this->username\r\n");
            fputs($this->telnet, "$this->password\r\n");
            fputs($this->telnet, "enable\r\n");
            fputs($this->telnet, "config\r\n");
            fputs($this->telnet, "interface ethernet 1/g$port->port\r\n");
            fputs($this->telnet, "switchport mode $mode\r\n");

            if( $mode == 'access' )
            {
                fputs($this->telnet, "switchport $mode vlan $vlans\r\n");
            }
            else
            {
                fputs($this->telnet, "switchport $mode allowed vlan add $vlans $tagged\r\n");
            }

            fputs($this->telnet, "exit\r\n");
            fputs($this->telnet, "exit\r\n");

            if( $saveConfig )
            {
                fputs($this->telnet, "copy running-config startup-config\r\n");
                fputs($this->telnet, "y\r\n");
            }

            fclose( $this->telnet );
        }

        $this->updateVlans( $port, request()->vlans, $mode, $vlans );

        return;
    }

    /*
    *   change the port vlans 
    */
    public function changeVlans( $port ) 
    {
        /* init */
        if( isset( request()->vlans[ 'vlan' ] ) ) 
        {
            $vlans = request()->vlans[ 'vlan' ];
        }
        else 
        {
            $vlans = implode( ',', Arr::pluck( request()->vlans, 'vlan') );
        }

        $saveConfig = request()->saveConfig;
        $tagged = request()->tagged ? 'tagged' : '';
        $mode = request()->mode;

        /* Start Telnet Client */
        $this->startTelnet( $port->switch->ip_address );

        if( $this->telnet )
        {  
            stream_set_timeout( $this->telnet, 2 );

            /* get ports */
            fputs($this->telnet, "$this->username\r\n");
            fputs($this->telnet, "$this->password\r\n");
            fputs($this->telnet, "enable\r\n");
            fputs($this->telnet, "config\r\n");
            fputs($this->telnet, "interface ethernet 1/g$port->port\r\n");

            if( $mode == 'access' )
            {
                fputs($this->telnet, "switchport $mode vlan $vlans\r\n");
            }
            else
            {
                fputs($this->telnet, "switchport $mode allowed vlan add $vlans $tagged\r\n");
            }

            fputs($this->telnet, "exit\r\n");
            fputs($this->telnet, "exit\r\n");

            if( $saveConfig )
            {
                fputs($this->telnet, "copy running-config startup-config\r\n");
                fputs($this->telnet, "y\r\n");
            }

            fclose( $this->telnet );
        }

        $this->updateVlans( $port, request()->vlans, $mode, $vlans );

        return;
    }

    private function updateVlans( $port, $vlansArray, $mode, $vlans ) 
    {

        $insert = [];

        /* create the new vlan maps array */
        if( isset( $vlansArray[ 'id' ] ) )
        {
            $insert[] = $vlansArray[ 'id' ];
        }
        else 
        {
            foreach( $vlansArray as $vlan )
            {
                $insert[] = $vlan[ 'id' ];
            }
        }

        /* remove old vlans add new */
        $port->vlans()->sync( $insert );

        /* update port */
        $port->mode = $mode;
        $port->last_updated = Carbon::now();
        $port->save();

        /* lets add the history */
        PortHistory::insert( [
            'port_id'       =>  $port->id,
            'user_id'       =>  auth()->user()->id,
            'info'          =>  'Changed mode to ' . $mode . '. Assigned the following vlan(s):' . $vlans,
            'created_at'    =>  Carbon::now()
        ] );

        return;
    }

}