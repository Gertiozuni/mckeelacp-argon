<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\ClassroomFile;

class AppleClassroomMail extends Mailable
{
    use Queueable, SerializesModels;

    public $changes;
    public $user;
    public $campuses;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $changes, $user, $campuses )
    {
        $this->changes = $changes;
        $this->user = $user;
        $this->campuses = $campuses->keyBy('id');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $archive = ClassroomFile::latest()->first();

        $file = storage_path( 'app/' . $archive->path . 'results.zip' );
        
        return $this->markdown('mail.appleclassroom')->subject( 'Apple Classroom Results' )->attach( $file );
    }
}
