<?php

namespace App\Exceptions;

/**
 * Class InvalidRouteMiddleware
 *
 * @package App\Exceptions
 */
class InvalidRouteMiddleware extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = 'Route middlewares must be expressed as a list of classes namespaces.';
    }

}