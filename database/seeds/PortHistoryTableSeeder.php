<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\PortHistory;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class PortHistoryTableSeeder extends Seeder
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

        $totalUsers = DB::connection( 'legacy' )->table( 'network_ports_history' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' ports history to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' ports history/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'network_ports_history' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' ports history' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $port = PortHistory::find( $legacyItem->id );
                
                if ( ! $port )
                {
                    $port = new PortHistory;
                }

                /* Basic fields.. */
                $port->id = $legacyItem->id;
                $port->port_id = $legacyItem->port_id;
                $port->info = $legacyItem->info;
                $port->user_id = $legacyItem->user_id;
                $port->created_at = $legacyItem->created_at;

                $port->save();
            }
            \DB::commit();
        }
    }
}
