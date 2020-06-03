<?php

namespace App\Exceptions;

/**
 * Class InvalidDbCredentials
 *
 * @package App\Exceptions
 */
class InvalidDbCredentials extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = sprintf( 'The following variables are required bat mising: %s', $message );
    }

}