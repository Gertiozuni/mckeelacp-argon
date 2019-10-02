<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\NSwitch;

class SwitchSyncMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messages;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $emailMessage )
    {
        $this->messages = $emailMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        foreach( $this->messages as $ip => $value )
        {
            if( isset( $value[ 'ports' ] ) )
            {
                $switch = NSwitch::where( 'ip_address', $ip )->with( 'ports' )->first();

                foreach( $value[ 'ports' ] as $port => $change )
                { 
                    $p = $switch->ports->where( 'port', $port )->first();
                    $this->messages[ $ip ][ 'ports' ][ $port ][ 'description' ] = '';
                    $this->messages[ $ip ][ 'ports' ][ $port ][ 'description' ] = $p->description ? $p->description : 'Unknown';
                }
            }
        }
        return $this->markdown( 'mail.SwitchSync' );
    }
}
