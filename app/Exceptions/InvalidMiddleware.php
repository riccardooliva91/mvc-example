<?php

namespace App\Exceptions;

/**
 * Class InvalidMiddleware
 *
 * @package App\Exceptions
 */
class InvalidMiddleware extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = sprintf( '%s is not a valid middleware.', $message );
    }

}