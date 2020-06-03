<?php

namespace App;

/**
 * Class Request
 *
 * That's a wrapper around the $_REQUEST and $_SERVER super globals. This simplifies our life a little bit since
 * super globals are "dangerous" to handle and some data depends on the server configuration.
 *
 * @package App
 */
class Request
{

    /**
     * $_REQUEST
     * @var array
     */
    protected $request = [];

    /**
     * $_SERVER
     * @var array
     */
    protected $server = [];

    /**
     * Custom values
     *
     * @var array
     */
    protected $custom = [];


    /**
     * Request constructor.
     * In case you need to forge a request yourself, this is public.
     *
     * @param  array  $request
     * @param  array  $server
     */
    public function __construct( array $request, array $server )
    {
        $this->request = $request;
        $this->server  = $server;
    }

    /**
     * Generate an object reading the environment
     *
     * @return Request
     */
    public static function createFromEnvironment() : Request
    {
        return new self( $_REQUEST, $_SERVER );
    }

    /**
     * Get a request input value
     *
     * @param  string  $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getInput( string $key, $default = null )
    {
        if ( isset( $this->custom[ $key ] ) ) {
            return $this->custom[ $key ];

        }
        foreach ( [ $this->request, $this->server ] as $var ) {
            foreach ( [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ] as $group ) {
                if ( isset( $this->$var[ $group ] ) && isset( $this->$var[ $group ][ $key ] ) ) {
                    return $this->$var[ $group ][ $key ];
                }
            }
        }

        return $default;
    }

    /**
     * Set a custom value into the request. If present it will not be overwritten unless
     * the flag is passed as true.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  bool  $force
     *
     * @return $this
     */
    public function set( string $key, $value, $force = false ) : self
    {
        if ( ! isset( $this->custom[ $key ] ) || $force ) {
            $this->custom[ $key ] = $value;
        }

        return $this;
    }

    /**
     * Get the URI
     *
     * @return string
     */
    public function getRequestUri() : string
    {
        return rtrim( $this->request['REQUEST_URI'] ?? $this->server['REQUEST_URI'] ?? '', '/' ) . '/';
    }

    /**
     * Get the current request method (without considering pseudo-request methods)
     *
     * @return string
     */
    public function getRawRequestMethod() : string
    {
        return $this->server['REQUEST_METHOD'] ?? '';
    }

    /**
     * Get the current request method (pseudo-request are allowed)
     *
     * @return string
     */
    public function getRequestMethod() : string
    {
        $method = $this->getRawRequestMethod();
        if ( 'POST' == $method ) {
            $restful = $this->getInput( '_method' );
            if ( ! empty( $restful ) ) {
                $method = $restful;
            }
        }

        return trim( $method );
    }

    /**
     * Get the website domain
     *
     * @return string
     */
    public function getDomain() : string
    {
        return $this->server['SERVER_NAME'] ?? $this->server['HTTP_HOST'];
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getHomeUrl() : string
    {
        $protocol = isset( $this->server['HTTPS'] ) && $this->server['HTTPS'] != 'off' ? 'https' : 'http';

        return sprintf( '%s://%s', $protocol, $this->getDomain() );
    }

    /**
     * Get the current request URL
     *
     * @return string
     */
    public function getCurrentUrl() : string
    {
        return sprintf( '%s%s', $this->getHomeUrl(), $this->getRequestUri() );
    }

}