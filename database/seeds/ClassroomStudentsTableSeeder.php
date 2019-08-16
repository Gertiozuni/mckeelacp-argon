<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\ClassroomStudent;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class ClassroomStudentsTableSeeder extends Seeder
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

        $totalUsers = DB::connection( 'legacy' )->table( 'classroom_students' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' students to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' students/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'classroom_students' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' students' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $student = ClassroomStudent::find( $legacyItem->id );
                
                if ( ! $student )
                {
                    $student = new ClassroomStudent;
                }

                /* Basic fields.. */
                $student->id = $legacyItem->id;
                $student->student_id = $legacyItem->student_id;
                $student->first_name = $legacyItem->first_name;
                $student->last_name = $legacyItem->last_name;
                $student->grade = $legacyItem->grade;
                $student->campus_id = $legacyItem->campus_id;

                $student->save();
            }
            \DB::commit();
        }
    }
}
