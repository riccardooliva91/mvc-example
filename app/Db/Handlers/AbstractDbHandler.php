<?php

namespace App\Db\Handlers;

use App\Config\Env;
use App\Exceptions\InvalidDbCredentials;
use App\Exceptions\InvalidDbCredentialMissingDatabase;

/**
 * Class AbstractDbHandler
 *
 * @package App\Db\Handlers
 *
 * These objects will perform simple operations in the database. In larger, more
 * demanding projects you will want to split grammars, query builders and
 * so on into separate entities, but for now this is fine.
 */
abstract class AbstractDbHandler
{

    /**
     * AbstractDbHandler constructor.
     */
    protected function __construct()
    {
        // We don't want to instantiate this directly from the outside
    }

    /**
     * Merge provided credentials with the ones from the environment
     *
     * @param  array  $params
     *
     * @return array
     *
     * @throws InvalidDbCredentialMissingDatabase|InvalidDbCredentials
     */
    protected function prepareCredentials( array $params ) : array
    {
        $defaults = [
            'host'     => Env::get( 'DB_HOST' ),
            'user'     => Env::get( 'DB_USER' ),
            'password' => Env::get( 'DB_PASSWORD' ),
            'database' => Env::get( 'DB_DATABASE' ),
        ];
        $params   = array_merge( $defaults, $params );

        $this->credentialsValidationGate( $params );

        return $params;
    }

    /**
     * Perform validation upon provided credentials, throw errors if something's wrong
     *
     * @param $params
     *
     * @return void
     *
     * @throws InvalidDbCredentials|InvalidDbCredentialMissingDatabase
     */
    protected function credentialsValidationGate( $params ) : void
    {
        $missing = [];
        foreach ( [ 'host', 'user', 'password' ] as $var ) {
            if ( empty( $params[ $var ] ) ) {
                $missing[] = $var;
            }
        }

        if ( ! empty( $missing ) ) {
            throw new InvalidDbCredentials( implode( ', ', $missing ) );
        }

        if ( empty( $params['database'] ) ) {
            throw new InvalidDbCredentialMissingDatabase();
        }
    }

    /**
     * Connect to the database
     *
     * @param  array  $params
     *
     * @return AbstractDbHandler
     *
     * @throws InvalidDbCredentials|InvalidDbCredentialMissingDatabase
     */
    public abstract static function getConnection( array $params = [] ) : AbstractDbHandler;

    /**
     * Close connection
     *
     * @return void
     */
    public abstract function closeConnection() : void;

    /**
     * Get by primary key
     *
     * @param  string  $table
     * @param  string  $primaryKey
     * @param $primaryValue
     *
     * @return array
     */
    public abstract function find( string $table, string $primaryKey, $primaryValue ) : array;

    /**
     * Create a row
     *
     * @param  string  $table
     * @param  array  $params
     *
     * @return bool
     */
    public abstract function create( string $table, array $params ) : bool;

    /**
     * Update a row
     *
     * @param  string  $table
     * @param  string  $primaryKey
     * @param  array  $params
     *
     * @return array
     */
    public abstract function update( string $table, string $primaryKey, array $params ) : array;

    /**
     * Delete a row
     *
     * @param  string  $table
     * @param  string  $primaryKey
     * @param  int  $primaryValue
     *
     * @return bool
     */
    public abstract function delete( string $table, string $primaryKey, int $primaryValue ) : bool;

    /**
     * Execute a raw statement
     *
     * @param  string  $statement
     * @param  array  $params
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public abstract function raw( string $statement, array $params );

}