<?php

namespace App\Router;

use App\Request;
use App\Registry;
use App\Exceptions\InvalidRouteHandler;
use App\Exceptions\InvalidRouteMiddleware;

/**
 * Class Router
 *
 * Pretty simple, but it will do the job.
 *
 * @package App\Router
 */
class Router
{

    /**
     * @var array
     */
    protected $registered_routes = [];

    /**
     * @var null|Route
     */
    protected $current_route = null;

    /**
     * @var Request
     */
    protected $request = null;


    /**
     * Router constructor.
     *
     * @param  Request  $request
     */
    private function __construct( Request $request )
    {
        $this->request = $request;
    }

    /**
     * Get a Router instance from the registry
     *
     * @return static
     */
    public static function getInstance() : self
    {
        $registry = Registry::create();
        if ( ! $registry->has( 'router' ) ) {
            // We don't need to (and we shouldn't) create router instances manually, so we can instantiate
            // the request here and pass it to the constructor, without injecting that object in this method.
            // Dependency injection is surely good for testing, but the `createFromEnvironment` method itself
            // simply reads the super globals, so if we ever need to test something and we really can'p't mock
            // the router, we can just alter the super globals in an isolated test, if we need to.
            $router = new self( Request::createFromEnvironment() );
            $registry->set( 'router', $router );
        }

        return $router ?? $registry->get( 'router' );
    }

    /**
     * Register a GET route
     *
     * @param  string  $name
     * @param  string  $path
     * @param  callable|string  $handler
     * @param  array  $middlewares
     *
     * @return void
     */
    public function get( string $name, string $path, $handler, array $middlewares = [] ) : void
    {
        $this->add( 'GET', $name, $path, $handler, $middlewares );
    }

    /**
     * Register a POST route
     *
     * @param  string  $name
     * @param  string  $path
     * @param  callable|string  $handler
     * @param  array  $middlewares
     *
     * @return void
     */
    public function post( string $name, string $path, $handler, array $middlewares = [] ) : void
    {
        $this->add( 'POST', $name, $path, $handler, $middlewares );
    }

    /**
     * Register a PUT route
     *
     * @param  string  $name
     * @param  string  $path
     * @param  callable|string  $handler
     * @param  array  $middlewares
     *
     * @return void
     */
    public function put( string $name, string $path, $handler, array $middlewares = [] ) : void
    {
        $this->add( 'PUT', $name, $path, $handler, $middlewares );
    }

    /**
     * Register a PATCH route
     *
     * @param  string  $name
     * @param  string  $path
     * @param  callable|string  $handler
     * @param  array  $middlewares
     *
     * @return void
     */
    public function patch( string $name, string $path, $handler, array $middlewares = [] ) : void
    {
        $this->add( 'PATCH', $name, $path, $handler, $middlewares );
    }

    /**
     * Register a DELETE route
     *
     * @param  string  $name
     * @param  string  $path
     * @param  callable|string  $handler
     * @param  array  $middlewares
     *
     * @return void
     */
    public function delete( string $name, string $path, $handler, array $middlewares = [] ) : void
    {
        $this->add( 'DELETE', $name, $path, $handler, $middlewares );
    }

    /**
     * Get one of the registered routes as a Route instance, if available
     *
     * @param  string  $name
     *
     * @return Route|null
     */
    public function getRoute( string $name )
    {
        foreach ( $this->registered_routes as $route ) {
            if ( $name === $route['name'] ) {
                return new Route( $route );
            }
        }

        return null;
    }

    /**
     * Get the current route
     *
     * @return Route|null
     */
    public function getCurrentRoute()
    {
        return $this->current_route;
    }

    /**
     * Replace the old registry router with the current one. This
     * should be done after the routes registration only once in the
     * application request cycle
     *
     * @return void
     */
    public function save() : void
    {
        $registry = Registry::create();
        $registry->unset( 'router' );
        $registry->set( 'router', $this );
    }

    /**
     * A helper function which detects placeholders
     *
     * @param  string  $chunk
     *
     * @return bool
     */
    public static function isPlaceholder( string $chunk )
    {
        return '{' === $chunk[0] && '}' == substr( $chunk, - 1 );
    }

    /**
     * A helper function which formats placeholders
     *
     * @param  string  $chunk
     *
     * @return string
     */
    public static function sanitizePlaceholder( string $chunk ) : string
    {
        $chunk = str_replace( [ '{', '}' ], '', $chunk );
        $chunk = str_replace( [ ' ', '-' ], '_', $chunk );

        return $chunk;
    }

    /**
     * Find the first route that matches the current request
     *
     * @return void
     */
    public function resolve() : void
    {
        $uri             = $this->request->getRequestUri();
        $uri_parts_count = count( explode( '/', $uri ) );
        $method          = strtolower( $this->request->getRequestMethod() );

        foreach ( $this->registered_routes as $registered_route ) {
            // If the method doesn't match, skip
            if ( strtolower( $registered_route['method'] ) !== $method ) {
                continue;
            }

            // In case there is a simple match, no need for the regex ahead
            if ( $registered_route['path'] === $uri ) {
                $this->current_route = new Route( $registered_route );
                break;
            }

            // If the url depth doesn't match, that's not if for sure. We can skip useless checks.
            if ( $uri_parts_count !== count( explode( '/', $registered_route['path'] ) ) ) {
                continue;
            }

            // If we got here, the route we are currently analyzing MAY be the one. We'll perform the check using regex
            $pattern = sprintf( '#%s#', $registered_route['path'] );
            $match   = preg_match_all( $pattern, $uri, $matches );
            if ( $match ) { // That's it!
                if ( 1 < count( $matches ) ) {
                    array_shift( $matches ); // The first element is the full uri, we don't need it
                    $matches          = array_values( $matches );
                    $variables_values = [];
                    // We want only the values, so we'll get them out of the $matches structure
                    array_walk_recursive( $matches, function ( $leaf ) use ( &$variables_values ) {
                        $variables_values[] = $leaf;
                    } );
                    $variables_count = count( $registered_route['variables_names'] );
                    // Now we create an associative array, where the keys are the variables names we identified before
                    for ( $i = 0; $i < $variables_count; $i ++ ) {
                        $key   = $registered_route['variables_names'][ $i ];
                        $value = $variables_values[ $i ];

                        $registered_route['variables'][ $key ] = $value;
                    }
                }

                $this->current_route = new Route( $registered_route );
                break;
            }
        }
    }

    /**
     * A helper we use so we don't have to specify the whole class
     * namespace when we register the routes
     *
     * @return string
     */
    protected function getControllersNamespace() : string
    {
        return '\App\Controllers\\';
    }

    /**
     * Add a route to the registered ones
     *
     * @param  string  $method
     * @param  string  $name
     * @param  string  $path
     * @param $handler
     * @param  array  $middlewares
     *
     * @return void
     *
     * @throws InvalidRouteHandler|InvalidRouteMiddleware
     */
    private function add( string $method, string $name, string $path, $handler, array $middlewares ) : void
    {
        $path_and_variables = $this->getPathAndVariables( $path );

        $this->registered_routes[] = [
            'method'          => $method,
            'name'            => $name,
            'original_path'   => $path,
            'path'            => $path_and_variables['path'],
            'variables_names' => $path_and_variables['variables'],
            'variables'       => [], // We'll populate this if the route matches
            'handler'         => $this->getHandler( $handler ),
            'middlewares'     => $this->checkMiddlewaresArray( $middlewares ),
        ];
    }

    /**
     * Validate the handler param, and prepare it for the route
     *
     * @param  callable|string  $handler
     *
     * @return callable|string[]
     *
     * @throws InvalidRouteHandler
     */
    private function getHandler( $handler )
    {
        if ( is_string( $handler ) ) {
            $handler = explode( '::', $handler );
            if ( 2 !== count( $handler ) ) {
                throw new InvalidRouteHandler( implode( '::', $handler ) );
            }
        } elseif ( ! is_callable( $handler ) ) {
            throw new InvalidRouteHandler( $handler );
        }

        return $handler;
    }

    /**
     * Prepare the path regex and identify possible variables
     *
     * @param  string  $path
     *
     * @return array
     */
    private function getPathAndVariables( string $path ) : array
    {
        $variables = [];
        $path      = explode( '/', $path );
        foreach ( $path as &$chunk ) {
            if ( self::isPlaceholder( $chunk ) ) {
                $variable    = self::sanitizePlaceholder( $chunk );
                $variables[] = $variable;
                $chunk       = '([0-9A-Za-z]+)';
            }
        }

        $path = '/' . implode( '/', array_filter( $path ) ) . '/$';
        $path = str_replace( '//', '/', $path ); // Homepage edge case

        return [
            'path'      => $path,
            'variables' => $variables
        ];
    }

    /**
     * Confirm that $middlewares is an array of strings. If those are not
     * valid classes namespaces, we'll throw an exception later
     *
     * @param  array  $middlewares
     *
     * @return array
     *
     * @throws InvalidRouteMiddleware
     */
    private function checkMiddlewaresArray( array $middlewares ) : array
    {
        foreach ( $middlewares as $middleware ) {
            if ( ! is_string( $middleware ) ) {
                throw new InvalidRouteMiddleware();
            }
        }

        return $middlewares;
    }

}