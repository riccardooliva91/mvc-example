<?php

namespace App\Exceptions;

/**
 * Class InvalidEnvironmentVariable
 *
 * @package App\Exceptions
 */
class InvalidEnvironmentVariable extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = sprintf( 'Invalid environment signature: %s', $message );
    }

}