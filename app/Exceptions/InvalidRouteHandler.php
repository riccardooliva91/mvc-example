<?php

namespace App\Exceptions;

/**
 * Class InvalidRouteHandler
 *
 * @package App\Exceptions
 */
class InvalidRouteHandler extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = sprintf( 'Invalid route handler: %s', $message );
    }

}