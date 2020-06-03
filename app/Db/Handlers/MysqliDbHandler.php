<?php

namespace App\Db\Handlers;

/**
 * Class MysqliDbHandler
 *
 * @package App\Db\Handlers
 */
class MysqliDbHandler extends AbstractDbHandler
{

    /**
     * @var \mysqli
     */
    private $connection = null;


    /**
     * @inheritDoc
     */
    public static function getConnection( array $params = [] ) : AbstractDbHandler
    {
        $obj    = new self();
        $params = $obj->prepareCredentials( $params );

        $obj->connection = new \mysqli( $params['host'], $params['user'], $params['password'], $params['database'] );

        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function closeConnection() : void
    {
        if ( ! empty( $this->connection ) ) {
            $this->connection->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function find( string $table, string $primaryKey, $primaryValue ) : array
    {
        $bindings  = $this->getBindings( [ $primaryKey => $primaryValue ] );
        $statement = sprintf( "SELECT * FROM `%s` WHERE %s = ? LIMIT 1;", $table, $primaryKey );
        $result    = $this->launchQuery( $statement, $bindings )->get_result();

        return false !== $result ? $result->fetch_row() : [];
    }

    /**
     * @inheritDoc
     */
    public function create( string $table, array $params ) : bool
    {
        $bindings     = $this->getBindings( $params );
        $placeholders = implode( ', ', array_fill( 0, count( $bindings['values'] ), '?' ) );
        $statement    = sprintf( 'INSERT INTO `%s` (%s) VALUES (%s);', $table, implode( ', ', array_keys( $params ) ), $placeholders );

        return 1 == $this->launchQuery( $statement, $bindings )->affected_rows;
    }

    /**
     * @inheritDoc
     */
    public function update( string $table, string $primaryKey, array $params ) : array
    {
        $bindings            = $this->getBindings( $params );
        $values_placeholders = array_map( function ( $key ) {
            return "`$key` = ?";
        }, array_keys( $params ) );
        $values_placeholders = implode( ', ', $values_placeholders );
        $statement           = sprintf( "UPDATE `%s` SET %s WHERE `%s` = '%s';", $table, $values_placeholders, $primaryKey, $params[ $primaryKey ] ?? null );

        $result = $this->launchQuery( $statement, $bindings );

        return 0 == $result->error ? $this->find( $table, $primaryKey, $params[ $primaryKey ] ) : [];
    }

    /**
     * @inheritDoc
     */
    public function delete( string $table, string $primaryKey, int $primaryValue ) : bool
    {
        $bindings  = $this->getBindings( [ $primaryKey => $primaryValue ] );
        $statement = sprintf( "DELETE FROM `%s` WHERE %s = ?;", $table, $primaryKey );
        $result    = $this->launchQuery( $statement, $bindings );

        return 1 == $result->affected_rows;
    }

    /**
     * @inheritDoc
     */
    public function raw( string $statement, array $params )
    {
        if ( ! empty( $params ) ) {
            $params = $this->getBindings( $params );
        }

        $query_result = false;
        $result       = $this->launchQuery( $statement, $params );
        $stmt_result  = $result->get_result();
        if ( false !== $stmt_result ) {
            $query_result = [];
            while ( $row = $stmt_result->fetch_array( MYSQLI_ASSOC ) ) {
                $query_result[] = $row;
            }
        } elseif ( 0 == $result->error ?? 1 ) {
            $query_result = true;
        }

        return $query_result;
    }

    /**
     * Get types and values for an mysqli statement
     *
     * @param  array  $values
     *
     * @return array
     */
    protected function getBindings( array $values ) : array
    {
        $computed = [];
        foreach ( $values as $value ) {
            switch ( $value ) {
                case is_integer( $value ):
                    $computed[] = [ 'value' => $value, 'type' => 'i' ];
                    break;
                case is_float( $value ):
                    $computed[] = [ 'value' => $value, 'type' => 'd' ];
                case is_string( $value ):
                default:
                    $computed[] = [ 'value' => $value, 'type' => 's' ];
                    break;
            }
        }

        return [
            'types'  => array_column( $computed, 'type' ),
            'values' => array_column( $computed, 'value' ),
        ];
    }

    /**
     * Execute a statement and get the result. We fail silently because we manage
     * the error form the caller, but throwing an exception is perfectly fine too.
     *
     * @param  string  $statement
     * @param  array  $bindings
     *
     * @return bool|\mysqli_stmt
     */
    protected function launchQuery( string $statement, array $bindings = [] )
    {
        $query = $this->connection->stmt_init();
        $query->prepare( $statement );
        if ( ! empty( $bindings ) ) {
            $query->bind_param( implode( '', $bindings['types'] ), ...$bindings['values'] );
        }

        try {
            $query->execute();

            return $query;
        } catch ( \Exception $e ) {
            return false;
        }
    }

}