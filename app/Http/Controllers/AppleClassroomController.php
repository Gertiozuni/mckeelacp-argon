<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use App\Models\Campus;
use App\Models\ClassroomStudent;
use App\Models\ClassroomTeacher;
use App\Models\ClassroomFile;

use App\Imports\ScheduleImport;
use App\Exports\ScheduleExport;

use Maatwebsite\Excel\Facades\Excel;

use App\Mail\AppleClassroomMail;

use Carbon\Carbon;
use Auth;

class AppleClassroomController extends Controller
{
    /**
     *  Shows apple classroom upload page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd( config('app.name') );
        return view( 'appleclassroom.index' );
    }

    /**
     *  Uploads oringal spreadsheet files
     *  METHOD: POST
     */
    public function upload( Request $request )
    {
        $directory = 'appleclassroom/' . date( 'Ymd' ) . '/originals/';

        foreach( $request->file as $file ) 
        {   
            $original = $file->getClientOriginalName( '.' . $file->getExtension() );
            $file->storeAs( $directory, $original );
        }

        return response()->json( [ 'Success' => 'Success' ] );
    }

    /**
     *  Convert excel files to csv for apple classroom upload
     *  METHOD: POST
     */
    public function update( Request $request )
    {
        /* Init */
        $date = date( 'Ymd' );
        $campuses = Campus::all();
        $directory = storage_path( 'app/appleclassroom/' . $date . '/originals/' );

        $staff[] = [ "person_id" , "person_number" , "first_name" , "middle_name" , "last_name" , "email_address" , "sis_username" , "location_id" ];
        $courses[] = [ "course_id" , "course_number" , "course_name" , "location_id" ];
        $classes[] = [ "class_id" , "class_number" , "course_id" , "instructor_id" , "instructor_id_2" , "instructor_id_3" , "location_id"];
        $rosters[] = [ "roster_id" , "class_id" , "student_id"  ];
        $students[] = [ "person_id" , "person_number" , "first_name" , "middle_name" , "last_name" , "grade_level" , "email_address" , "sis_username" , "password_policy" , "location_id" ];
        $locations[] = [ "location_id" , "location_name" ];
        $data = [];
        
        /*******************************************************************************************
            Setup locations.csv
        /*******************************************************************************************/
        foreach( $campuses as $campus )
        {
            $locations[] = [
                'location_id'   => $campus->code,
                'location_name' => $campus->name,
            ];
        }

        /*******************************************************************************************
            Uses the schedules csv files. Creates rosters, classes, schedules, staff
        /*******************************************************************************************/
        $matSchedule = Excel::toArray( new ScheduleImport, $directory . 'mat.csv' );
        $data[ 'mat' ] = $this->convertSchedule( $matSchedule, $campuses->where( 'abbreviation', 'MAT' )->first()->code );

        $macSchedule = Excel::toArray( new ScheduleImport, $directory . 'mac.csv' );
        $data[ 'mac' ] = $this->convertSchedule( $macSchedule, $campuses->where( 'abbreviation', 'MAC' )->first()->code );
        
        $smaSchedule = Excel::toArray( new ScheduleImport, $directory . 'sma.csv' );
        $data[ 'sma' ] = $this->convertSchedule( $smaSchedule, $campuses->where( 'abbreviation', 'SMA' )->first()->code );

        /* set the arrays */
        foreach( $data as $school )
        {
            foreach( $school[ 'staff' ] as $s )
            {
                $staff[] = $s;
            }
            foreach( $school[ 'courses' ] as $co )
            {
                $courses[] = $co;
            }
            foreach( $school[ 'classes' ] as $cl )
            {
                $classes[] = $cl;
            }
            foreach( $school[ 'rosters' ] as $r )
            {
                $rosters[] = $r;
            }
        } 

        /*******************************************************************************************
            Uses the students xls files. Creates students
        /*******************************************************************************************/
        $studentsSheet[] = [
            'mat' => Excel::toArray( new ScheduleImport, $directory . 'mat.xls' ),
            'mac' => Excel::toArray( new ScheduleImport, $directory . 'mac.xls' ),
            'sma' => Excel::toArray( new ScheduleImport, $directory . 'sma.xls' )
        ];
        
        /* fancy loops to add to the array */
        foreach( $studentsSheet as $key => $campus )
        {
            foreach( $campus as $studentList )
            {
                foreach( $studentList[ 0 ] as $index => $row )
                {

                    if( $index == 0 ) {
                        continue;
                    }

                    $students[] = [
                        'person_id'         =>  (int) $row[ 0 ], // id number
                        'person_number'     =>  (int) $row[ 0 ], // id number
                        'first_name'        =>  $row[ 2 ], // first name
                        'middle_name'       =>  '',
                        'last_name'         =>  $row[ 1 ], // last name
                        'grade_level'       =>  $row[ 4 ] == 'KG' ? $row[ 4 ] : (int) $row[ 4 ] , // grade level
                        'email_address'     =>  '',
                        'sis_username'      =>  '',
                        'password_policy'   =>  '',
                        'location_id'       =>  (int) $row[ 6 ]   // school code
                    ];
                }
            }
        }

        /* lets make our new directory */
        $path = '/appleclassroom/' . $date . '/results/';

        /* store the files */
        Excel::store(new ScheduleExport( $staff ),  $path . 'staff.csv');
        Excel::store(new ScheduleExport( $classes ),  $path . 'classes.csv');
        Excel::store(new ScheduleExport( $rosters ),  $path . 'rosters.csv');
        Excel::store(new ScheduleExport( $students ),  $path . 'students.csv');
        Excel::store(new ScheduleExport( $courses ),  $path . 'courses.csv');
        Excel::store(new ScheduleExport( $locations ),  $path . 'locations.csv');

        /* Create the zip */
        $path = 'appleclassroom/' . $date . '/';
        $files = Storage::files( $path . 'results' );

        /* create zip of new files */
        $zip = new \ZipArchive;
        if ( $zip->open( storage_path( 'app/' . $path . 'results.zip' ), \ZipArchive::CREATE ) === TRUE ) 
        {
            foreach( $files as $file ) 
            {
                $name = str_replace( $path . 'results/', "", $file );
                $zip->addFile( storage_path( 'app/' . $path . 'results/' . $name ), 'results/' . $name );
            }

            $zip->close();
        }

        /* create zip of old files */
        $files = Storage::files( $path . 'originals' );
        $zip = new \ZipArchive;
        if ( $zip->open( storage_path( 'app/' . $path . 'originals.zip' ), \ZipArchive::CREATE ) === TRUE ) 
        {
            foreach( $files as $file ) 
            {
                $name = str_replace( $path . 'originals/', "", $file );
                $zip->addFile( storage_path( 'app/' . $path . 'originals/' . $name ), 'originals/' . $name );
            }
        
            $zip->close();
        }

        $changes = $this->detectChanges( $date, $campuses, $staff, $students );

        /* any new or removed students? */
        if( isset( $changes[ 'students' ] ) )
        {
            if( isset( $changes[ 'students' ][ 'removed' ] ) && count( $changes[ 'students' ][ 'removed' ] ) )
            {
                $deletedStudents = array_pluck( $changes[ 'students' ][ 'removed' ], 'student_id' );
                ClassroomStudent::whereIn( 'student_id', $deletedStudents )->delete();
            }

            if( isset( $changes[ 'students' ][ 'added' ] ) && count( $changes[ 'students' ][ 'added' ] ) )
            {
                ClassroomStudent::insert( $changes[ 'students' ][ 'added' ] );
            }
        }

        /* any new or removed teachers? */
        if( isset( $changes[ 'teachers' ] ) )
        {
            if( isset( $changes[ 'teachers' ][ 'removed' ] ) && count( $changes[ 'teachers' ][ 'removed' ] ) )
            {
                $deletedStaff = array_pluck( $changes[ 'teachers' ][ 'removed' ], 'staff_id' );
                ClassroomTeacher::whereIn( 'staff_id', $deletedStaff )->delete();
            }

            if( isset( $changes[ 'teachers' ][ 'added' ] ) && count( $changes[ 'teachers' ][ 'added' ] ) )
            {
                ClassroomTeacher::insert( $changes[ 'teachers' ][ 'added' ] );
            }
        }

        $user = Auth::user();

        /* store the record */
        $user->addArchive([
            'path' => $path,
            'students_removed' => isset( $changes[ 'students' ][ 'removed' ] ) ? count( $changes[ 'students' ][ 'removed' ] ) : 0,
            'students_added' => isset( $changes[ 'students' ][ 'added' ] ) ? count( $changes[ 'students' ][ 'added' ] ) : 0,
            'teachers_removed' => isset( $changes[ 'teachers' ][ 'removed' ] ) ? count( $changes[ 'teachers' ][ 'removed' ] ) : 0,
            'teachers_added' => isset( $changes[ 'teachers' ][ 'added' ] ) ? count( $changes[ 'teachers' ][ 'added' ] ) : 0,
            'created_at' => Carbon::now()
        ]);

        /* send the email */
        Mail::to( $user->email )->send( new AppleClassroomMail( $changes, $user, $campuses ) );

        /* if it wasnt jeff, send jeff an email */
        if( $user->email != 'jeffreyhosler@mckeelschools.com' )
        {
            Mail::to( 'jeffreyhosler@mckeelschools.com' )->send( new AppleClassroomMail( $changes, $user, $campuses ) );
        }

        return response([
            'user' => $user
        ]);
    }

    /**
     *  shows archive page
     *  METHOD: get
     */
    public function archives()
    {
        $archives = ClassroomFile::orderBy( 'created_at', 'desc' )->with( 'user' )->paginate( 10 );

        return view( 'appleclassroom.archives', compact( 'archives' ) );
    }

    /**
     *  download the file
     *  METHOD: get
     */
    public function download( Request $request, ClassroomFile $archive )
    {
        return response()->download( storage_path( 'app/' . $archive->path . $request->type . '.zip' ) );
    }  

    /*
    *   Creates the staff, courses, classes and roster csvs
    */
    private function convertSchedule( $schedule, $schoolCode )
    {

        $elem = [ 'PK', 'KG', '1', '2', '3', '4' ];
        $specials = [ 'CharlesSimpson', 'MarkCloutier', 'StacyBradley', 'KelleyCunningham', 'KimberlyBoyd', 'HayleyHurd', 'JencyMeche' ];
        $data = [ 'staff' => [], 'students' => [], 'courses' => [], 'classes' => [], 'rosters' => [] ];

        foreach( $schedule[ 0 ] as $index => $row )
        {

            /* skip the first row that contains titles */
            if( $index == 0 ) {
                continue;
            }

            /* example row
                row[ 0 ]-> Student ID
                row[ 1 ]-> Grade
                row[ 2 ]-> teacher / Period
                row[ 3 ]-> Course Title 
                row[ 4 ]-> Course Num
                row[ 5 ]-> Section Num
                row[ 6 ]-> Term
                row[ 7 ]-> Start Date
                row[ 8 ]-> End Date
            */


            /* gets the cell that has the teacher name and period */
            /* format: Period 2 - AC - 04 - MEGAN HORSWOOD WATSON-MORROW 
                      Period - Bell Sched - Section - Name */
            $cell = explode( ' - ', $row[ 2 ] );
            $teacherFull = ucwords( strtolower ( last( $cell ) ) );

            /* we dont want TBA teachers */
            if( strpos( $teacherFull, 'Tba' ) !== false )
            {
                continue;
            }

            /* lets get the teachers first and last name */
            $splitName = explode( ' ', $teacherFull );

            /* remove surname */
            if( in_array( strtolower( last( $splitName ) ), ['ii', 'jr', 'sr'] ) )
            {
                $keys = array_keys( $splitName );
                $last = end($keys);

                unset( $splitName[ $last ] );
            }

            $teacherFirst = $splitName[ 0 ];
            $teacherLast = last( $splitName );
            $teacherId = $teacherFirst . $teacherLast;

            /*
            *   lets creat the teachers array
            */
            if( ! isset( $data[ 'staff' ][ $teacherId ] ) )
            {
                $data[ 'staff' ][ $teacherId ] = [
                    'person_id'     => $teacherId, // first first . last name
                    'person_number' => '',
                    'first_name'    => $teacherFirst, // first name
                    'middle_name'   => '',
                    'last_name'     => $teacherLast, // last name
                    'email_address' => '',  
                    'sis_username'  => '',  
                    'location_id'   => $schoolCode
                ];
            }

            /* Elementary only has one teacher for multiple periods. so we dont want to add multiple classes */
            if( in_array( $row[ 1 ], $elem ) && ! in_array( $teacherId, $specials ) )
            {

                /* we basically want every student to be in their homeroom class and their special classes. not worried about periods and courses */
                $studentId = (int) $row[ 0 ];
                $period = str_replace( 'Period ', '', $cell[ 0 ] );

                /* sets the classes array to the teacher. */
                if( ! isset( $elemClasses[ $teacherId ] ) )
                {
                    $elemClasses[ $teacherId ] = [];
                }

                /* if the student is not in the teachers class, add it once. this bypasses periods and courses */
                if( ! in_array( $studentId, $elemClasses[ $teacherId ] ) )
                {

                    $elemClasses[ $teacherId ][] = $studentId;

                    /* since the student is not in the class yet. Add them. and lets make sure we arent making dupe courses */
                    if( ! isset( $data[ 'courses' ][ $teacherId ] ) )
                    {
                        // put into course array
                        $data[ 'courses' ][ $teacherId ] = [ 
                            'course_id'         => $teacherId,
                            'course_number'     => "",
                            'course_name'       => $teacherId,
                            'location_id'       => $schoolCode
                        ];
                    }

                    /* lets create the class if it doesnt exist */
                    if( ! isset( $data[ 'classes' ][ $teacherId ] ) )
                    {

                        // put into classes array 
                        $data[ 'classes' ][ $teacherId ] = [
                            'class_id'          => $teacherId,
                            'class_number'      => "",
                            'course_id'         => $teacherId,
                            'instructor_id'     => $teacherId,
                            'instructor_id_2'   => "",
                            'instructor_id_3'   => "",
                            'location_id'       => $schoolCode
                        ];
                    }
                }

                // put into rosters array
                $rostersArr = [];

                do{
                    $rosterId = ( mt_rand() / mt_getrandmax() ) * 10000;
                } while( in_array( $rosterId, $rostersArr ) );

                $rostersArr[] = $rosterId;

                $data['rosters'][] = [
                    'roster_id'     => $rosterId,
                    'class_id'      => $teacherId,
                    'student_id'    => (int) $row[ 0 ]
                ];
            }
            else
            {
                /* now lets work on courses and rosters */
                $period = str_replace( 'Period ', '', $cell[ 0 ] );
                $courseName = $period . ' - ' . $row[ 3 ] . ' - ' . $teacherId;
                $classId = $teacherId . ' - ' . $period;

                /* so we dont created multiple courses */
                if( ! isset( $data[ 'courses' ][ $period . '-' . $teacherId ] ) )
                {
                    // put into course array
                    $data[ 'courses' ][ $period . '-' . $teacherId ] = [ 
                        'course_id'         => $courseName,
                        'course_number'     => "",
                        'course_name'       => $courseName,
                        'location_id'       => $schoolCode
                    ];
                }
                /* so we dont created multiple courses */
                if( ! isset( $data[ 'classes' ][ $classId ] ) )
                {
                    // put into classes array 
                    $data[ 'classes' ][ $classId ] = [
                        'class_id'          => $classId,
                        'class_number'      => "",
                        'course_id'         => $courseName,
                        'instructor_id'     => $teacherId,
                        'instructor_id_2'   => "",
                        'instructor_id_3'   => "",
                        'location_id'       => $schoolCode
                    ];
                }

                $rostersArr = [];

                // put into rosters array
                do{
                    $rosterId = ( mt_rand() / mt_getrandmax() ) * 10000;
                } while( in_array( $rosterId, $rostersArr ) );

                $rostersArr[] = $rosterId;

                $data['rosters'][] = [
                    'roster_id'     => $rosterId,
                    'class_id'      => $classId,
                    'student_id'    => (int) $row[ 0 ]
                ];
            }
        }
        return $data;
    }

    /*
    *       Function used to see what is being added and removed
    */
    private function detectChanges( $date, $campuses, $staff, $students ) 
    {
        /* init */
        $currentStudents = ClassroomStudent::all();
        $studentKeys = $currentStudents->keyBy('student_id');
        $currentStaff = ClassroomTeacher::all();
        $staffKeys = $currentStaff->keyBy('staff_id');
        $results = [];
        $studentIds = array_pluck( $students, 'person_id' );
        $staffIds = array_pluck( $staff, 'person_id' );
        $keyed = $campuses->keyBy('code');

        /* remove headers */
        unset( $staff[ 0 ] );
        unset( $students[ 0 ] );

        // any new teachers being added?
        foreach( $staff as $teacher ) 
        {
            if( ! isset( $staffKeys[ $teacher[ 'person_id' ] ] ) ) 
            {
                $results[ 'teachers' ][ 'added' ][] = [
                    'staff_id'      =>  $teacher[ 'person_id' ],
                    'first_name'    =>  $teacher[ 'first_name' ],
                    'last_name'     =>  $teacher[ 'last_name' ],
                    'campus_id'     =>  $keyed[$teacher[ 'location_id' ]]->id
                ];
            }
        }

        // any teachers being removed?
        foreach( $currentStaff as $teacher ) 
        {
            if( ! in_array( $teacher[ 'staff_id'], $staffIds ) ) 
            {
                $results[ 'teachers' ][ 'removed' ][] = [
                    'staff_id'      =>  $teacher[ 'staff_id' ],
                    'first_name'    =>  $teacher[ 'first_name' ],
                    'last_name'     =>  $teacher[ 'last_name' ],
                    'campus_id'     =>  $teacher[ 'campus_id' ]
                ];
            }
        }

        // any new students being added?
        foreach( $students as $student ) 
        {
            if( ! isset( $studentKeys[ $student[ 'person_id' ] ] ) ) 
            {
                $results[ 'students' ][ 'added' ][] = [
                    'student_id'    =>  $student[ 'person_id' ],
                    'first_name'    =>  $student[ 'first_name' ],
                    'last_name'     =>  $student[ 'last_name' ],
                    'grade'         =>  $student[ 'grade_level' ],
                    'campus_id'     =>  $keyed[$student[ 'location_id' ]]->id
                ];
            }
        }

        // any students being removed?
        foreach( $currentStudents as $student ) 
        {
            if( ! in_array( $student[ 'student_id' ], $studentIds ) ) 
            {
                $results[ 'students' ][ 'removed' ][] = [
                    'student_id'    =>  $student[ 'student_id' ],
                    'first_name'    =>  $student[ 'first_name' ],
                    'last_name'     =>  $student[ 'last_name' ],
                    'grade'         =>  $student[ 'grade' ],
                    'campus_id'     =>  $student[ 'campus_id' ]
                ];
            }
        }

        return $results;
    }
}
