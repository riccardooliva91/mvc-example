<?php

namespace App\Config;

use App\Exceptions\InvalidConfig;
use App\Exceptions\InvalidConfigFile;

/**
 * Class Config
 *
 * @package App\Config
 *
 * A useful class which will read our configuration files
 */
class Config
{

    /**
     * The requested config file
     *
     * @var string
     */
    protected $config_file_name = '';

    /**
     * The content of the config file
     *
     * @var array
     */
    protected $config = [];


    /**
     * Config constructor.
     *
     * @param  string  $config_file_name
     */
    public function __construct( string $config_file_name )
    {
        $this->config_file_name = $config_file_name;
    }

    /**
     * Get an option. If nested, use the dots to navigate inside the array
     *
     * @param  string|array  $steps
     *
     * @return mixed
     *
     * @throws InvalidConfigFile|InvalidConfig
     */
    public function goTo( $steps )
    {
        $config = $this->getConfig();
        $value  = $config;
        if ( is_string( $steps ) ) {
            $steps = explode( '.', $steps );
        }
        $steps = array_reverse( $steps );

        while ( $step = array_pop( $steps ) ) {
            if ( ! array_key_exists( $step, $value ) ) {
                throw new InvalidConfig;
            }
            $value = $value[ $step ];
        }

        return $value;
    }

    /**
     * Quick way to obtain a config value
     *
     * @param  string  $path
     *
     * @return array|mixed
     *
     * @throws InvalidConfig|InvalidConfigFile
     */
    public static function get( string $path )
    {
        if ( empty( $path ) ) {
            throw new InvalidConfigFile();
        }

        $path = explode( '.', $path );
        $file = $path[0];
        array_shift( $path );

        return ( new self( $file ) )->goTo( $path );
    }

    /**
     * Lazy load the config file
     *
     * @return array
     *
     * @throws InvalidConfigFile
     */
    protected function getConfig() : array
    {
        if ( empty( $this->config ) ) {
            $file = __DIR__ . '/../../config/' . $this->config_file_name . '.php';
            if ( file_exists( $file ) ) {
                $this->config = require $file;
            } else {
                throw new InvalidConfigFile( $file );
            }
        }

        return $this->config;
    }

}