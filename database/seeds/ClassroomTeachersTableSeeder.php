<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\ClassroomTeacher;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class ClassroomTeachersTableSeeder extends Seeder
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

        $totalUsers = DB::connection( 'legacy' )->table( 'classroom_staff' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' teachers to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' teachers/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'classroom_staff' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' teachers' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $teacher = ClassroomTeacher::find( $legacyItem->id );
                
                if ( ! $teacher )
                {
                    $teacher = new ClassroomTeacher;
                }

                /* Basic fields.. */
                $teacher->id = $legacyItem->id;
                $teacher->staff_id = $legacyItem->staff_id;
                $teacher->first_name = $legacyItem->first_name;
                $teacher->last_name = $legacyItem->last_name;
                $teacher->campus_id = $legacyItem->campus_id;

                $teacher->save();
            }
            \DB::commit();
        }
    }
}
