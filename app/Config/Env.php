<?php

namespace App\Config;

use \App\Exceptions\InvalidEnvironmentVariable;

/**
 * Class Env
 *
 * @package App\Config
 */
class Env
{

    /**
     * Get var environment, silently defaults to null
     *
     * @param  string  $var
     *
     * @return mixed
     */
    public static function get( string $var )
    {
        return getenv( $var ) ?? null;
    }

    /**
     * Set an environment variable
     *
     * @param  string  $var
     * @param $value
     *
     * @return void
     */
    public static function set( string $var, $value ) : void
    {
        putenv( "$var=$value" );
    }

    /**
     * Remove a variable from the environment
     *
     * @param  string  $var
     *
     * @return void
     */
    public static function unset( string $var ) : void
    {
        putenv( $var );
    }

    /**
     * Set an environment variable (eaw string)
     *
     * @param  string  $pair
     *
     * @return void
     *
     * @throws InvalidEnvironmentVariable
     */
    public static function setRaw( string $pair ) : void
    {
        if ( false === strpos( $pair, '=' ) ) {
            throw new InvalidEnvironmentVariable( $pair );
        }
        putenv( $pair );
    }

}