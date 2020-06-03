<?php

namespace App\Db\Handlers;

/**
 * Class PdoDbHandler
 *
 * @package App\Db\Handlers
 */
class PdoDbHandler extends AbstractDbHandler
{

    /**
     * @var \PDO
     */
    private $connection = null;


    /**
     * @inheritDoc
     */
    public static function getConnection( array $params = [] ) : AbstractDbHandler
    {
        $obj     = new self();
        $params  = $obj->prepareCredentials( $params );
        $dsn     = sprintf( 'mysql:host=%s;dbname=%s;charset=utf8mb4', $params['host'], $params['database'] );
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_SILENT, // We're going to check error codes
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $obj->connection = new \PDO( $dsn, $params['user'], $params['password'], $options );

        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function closeConnection() : void
    {
        if ( ! empty( $this->connection ) ) {
            $this->connection = null; // PHP closes the PDO connection when the script ends the execution, but hey...
        }
    }

    /**
     * @inheritDoc
     */
    public function find( string $table, string $primaryKey, $primaryValue ) : array
    {
        $statement = sprintf( "SELECT * FROM `%s` WHERE %s = ? LIMIT 1;", $table, $primaryKey );
        $query     = $this->connection->prepare( $statement );
        $success   = $query->execute( [ $primaryValue ] );

        return $success ? $query->fetch() : [];
    }

    /**
     * @inheritDoc
     */
    public function create( string $table, array $params ) : bool
    {
        $placeholders = implode( ', ', array_fill( 0, count( $params ), '?' ) );
        $statement    = sprintf( 'INSERT INTO `%s` (%s) VALUES (%s);', $table, implode( ', ', array_keys( $params ) ), $placeholders );

        return $this->connection->prepare( $statement )->execute( array_values( $params ) );
    }

    /**
     * @inheritDoc
     */
    public function update( string $table, string $primaryKey, array $params ) : array
    {
        $values_placeholders = array_map( function ( $key ) {
            return "`$key` = ?";
        }, array_keys( $params ) );
        $values_placeholders = implode( ', ', $values_placeholders );
        $statement           = sprintf( "UPDATE `%s` SET %s WHERE `%s` = '%s';", $table, $values_placeholders, $primaryKey, $params[ $primaryKey ] ?? null );

        $query   = $this->connection->prepare( $statement );
        $success = $query->execute( array_values( $params ) );

        return $success ? $this->find( $table, $primaryKey, $params[ $primaryKey ] ) : [];
    }

    /**
     * @inheritDoc
     */
    public function delete( string $table, string $primaryKey, int $primaryValue ) : bool
    {
        $statement = sprintf( "DELETE FROM `%s` WHERE %s = ?;", $table, $primaryKey );

        return $this->connection->prepare( $statement )->execute( [ $primaryValue ] );
    }

    /**
     * @inheritDoc
     */
    public function raw( string $statement, array $params )
    {
        $query   = $this->connection->prepare( $statement );
        $success = $query->execute( array_values( $params ) );

        return $success ? $query->fetchAll() : false;
    }

}