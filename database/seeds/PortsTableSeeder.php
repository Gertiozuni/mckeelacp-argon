<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Port;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class PortsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
 		/* Init */
        Model::unguard();
        $output = new ConsoleOutput();
        $output->writeLn( '' );
        $perLoop = 100;

        $totalUsers = DB::connection( 'legacy' )->table( 'network_ports' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' legacy ports to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' ports/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'network_ports' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' ports' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $port = Port::find( $legacyItem->id );
                
                if ( ! $port )
                {
                    $port = new Port;
                }

                /* Basic fields.. */
                $port->id = $legacyItem->id;
                $port->port = $legacyItem->port;
                $port->description = $legacyItem->description;
                $port->switch_id = $legacyItem->switch_id;
                $port->mode = $legacyItem->mode;
                $port->fiber = $legacyItem->fiber;
                $port->active = $legacyItem->active;
                $port->last_updated = $legacyItem->last_updated;
                $port->checked_in = $legacyItem->checked_in;

                $port->save();
            }
            \DB::commit();
        }
    }
}
