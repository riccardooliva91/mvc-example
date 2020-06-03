<?php

namespace App\Exceptions;

/**
 * Class InvalidConfig
 *
 * @package App\Exceptions
 */
class InvalidConfig extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = 'Error while retrieving configuration: the specified ket does not exist.';
    }

}