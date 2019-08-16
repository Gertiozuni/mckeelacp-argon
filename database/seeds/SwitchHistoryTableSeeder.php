<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\SwitchHistory;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class SwitchHistoryTableSeeder extends Seeder
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

        $totalUsers = DB::connection( 'legacy' )->table( 'network_switch_history' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' switch history to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' switch history/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'network_switch_history' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' switch history' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $switch = SwitchHistory::find( $legacyItem->id );
                
                if ( ! $switch )
                {
                    $switch = new SwitchHistory;
                }

                /* Basic fields.. */
                $switch->id = $legacyItem->id;
                $switch->switch_id = $legacyItem->switch_id;
                $switch->info = $legacyItem->info;
                $switch->user_id = $legacyItem->user_id;
                $switch->created_at = $legacyItem->created_at;

                $switch->save();
            }
            \DB::commit();
        }
    }
}
