<?php

namespace App\Router;

/**
 * Class Route
 *
 * @package App\Router
 *
 * @property string $method
 * @property string $name
 * @property string $path
 * @property array $variables
 * @property callable|array $handler
 * @property array $middlewares
 */
class Route
{
    /**
     * @var string
     */
    protected $method = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var callable|array
     */
    protected $handler = null;

    /**
     * @var array
     */
    protected $middlewares = [];


    /**
     * Route constructor.
     *
     * We'll save just what we need. It might seem weird, but we won't
     * keep the uri of the route because if this class exists, the router
     * has already validated it. If we need the request uri we can use
     * the Request class in the controllers.
     * We keep the original pattern instead for URL generation.
     *
     * @param  array  $params
     */
    public function __construct( array $params )
    {
        $this->method      = $params['method'];
        $this->name        = $params['name'];
        $this->path        = $params['original_path'];
        $this->variables   = $params['variables'];
        $this->handler     = $params['handler'];
        $this->middlewares = $params['middlewares'];
    }

    /**
     * Generate an url with the provided wildcard values
     *
     * @param  array  $values
     *
     * @return string
     */
    protected function getUrl( array $values ) : string
    {
        $path = explode( '/', $this->path );
        foreach ( $path as &$chunk ) {
            if ( Router::isPlaceholder( $chunk ) ) {
                $variable = Router::sanitizePlaceholder( $chunk );
                $chunk    = $values[ $variable ];
            }
        }
        $path = implode( '/', $path );

        return $path;
    }

    /**
     * Get a class property
     *
     * @param  string  $var
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get( string $var )
    {
        if ( ! property_exists( $this, $var ) ) {
            throw new \Exception( 'Invalid property: ' . $var );
        }

        return $this->$var;
    }

}