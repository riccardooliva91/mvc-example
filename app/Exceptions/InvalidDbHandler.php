<?php

namespace App\Exceptions;

/**
 * Class InvalidDbHandler
 *
 * @package App\Exceptions
 */
class InvalidDbHandler extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = sprintf( 'Unknown database handler: %s', $message );
    }

}