<?php

namespace App;

/**
 * Class Registry
 *
 * @package App\Registry
 */
class Registry
{

    /**
     * @var Registry
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $registry = [];


    /**
     * Registry constructor.
     */
    private function __construct()
    {
        // No direct instantiation!
    }

    /**
     * Get a registry instance
     *
     * @return static
     */
    public static function create() : self
    {
        if ( empty( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Check if a key has been set
     *
     * @param  string  $class
     *
     * @return bool
     */
    public function has( string $class ) : bool
    {
        return ! empty( $this->registry[ $class ] );
    }

    /**
     * Get an object from the registry
     *
     * @param  string  $class
     *
     * @return mixed|null
     */
    public function get( string $class )
    {
        return $this->registry[ $class ] ?? null;
    }

    /**
     * Set a new object
     *
     * @param  string  $key
     * @param $object
     *
     * @return $this
     */
    public function set( string $key, $object )
    {
        $this->registry[ $key ] = $object;

        return $this;
    }

    /**
     * Unset an object, if it exists
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function unset( string $key )
    {
        if ( $this->has( $key ) ) {
            unset( $this->registry[ $key ] );
        }

        return $this;
    }

}