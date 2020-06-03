<?php

namespace App\Exceptions;

/**
 * Class InvalidConfigFile
 *
 * @package App\Exceptions
 */
class InvalidConfigFile extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = sprintf( 'The %s.php config file doesn\'t exist', $message );
    }

}