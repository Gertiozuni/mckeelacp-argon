<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\SwitchLog;
use App\Models\PortHistory;
use App\Models\SwitchHistory;

use Symfony\Component\Console\Output\ConsoleOutput;

class SeedSwitchLogsTable extends Seeder
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

        $total = DB::table( 'switch_history' )->count();
        $output->writeLn( 'Found ' . $total . ' switch histories to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' switch history/loop' );
        $output->writeLn( '' );

        for ( $i = 0; $i <= $total; $i += $perLoop )
        {
            $thisBatch = SwitchHistory::orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $total . "\tFound " . count( $thisBatch ) . ' switch history' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                SwitchLog::insert([
                    'switch_id' => $legacyItem->switch_id,
                    'port_id' => null,
                    'event' => $legacyItem->info,
                    'user_id' => $legacyItem->user_id,
                    'created_at' => $legacyItem->created_at
                ]);
            }
            \DB::commit();
        }

        $total = DB::table( 'port_history' )->count();
        $output->writeLn( 'Found ' . $total . ' port histories to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' port history/loop' );
        $output->writeLn( '' );

        for ( $i = 0; $i <= $total; $i += $perLoop )
        {
            $thisBatch = PortHistory::orderBy( 'id' )->offset( $i )->limit( $perLoop )->with('ports')->get();
            $output->writeLn( 'Batch ' . $i . '/' . $total . "\tFound " . count( $thisBatch ) . ' switch history' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                SwitchLog::insert([
                    'switch_id' => $legacyItem->ports->switch_id,
                    'port_id' => $legacyItem->port_id,
                    'event' => $legacyItem->info,
                    'user_id' => $legacyItem->user_id,
                    'created_at' => $legacyItem->created_at
                ]);
            }
            \DB::commit();
        }
    }
}
