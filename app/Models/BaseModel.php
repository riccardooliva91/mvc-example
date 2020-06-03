<?php

namespace App\Models;

use App\Interfaces\Jsonable;
use App\Interfaces\Arrayable;

/**
 * Class BaseModel
 *
 * @package App\Models
 */
abstract class BaseModel implements Arrayable, Jsonable, \IteratorAggregate
{

    /**
     * Fields definitions
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The model current values
     *
     * @var array
     */
    protected $model_values = [];

    /**
     * The DB table which holds this entity
     *
     * @var string
     */
    protected $table = '';


    /**
     * BaseModel constructor.
     *
     * @param  array  $values
     */
    public function __construct( array $values = [] )
    {
        $this->hydrate( $values );
    }

    /**
     * Fill the model from an array
     *
     * @param  array  $values
     *
     * @return $this
     */
    public function hydrate( array $values ) : self
    {
        if ( ! empty( $values ) ) {
            $keys   = array_flip( array_column( $this->fields, 'name' ) );
            $values = array_intersect_key( $values, $keys );

            $this->model_values = $this->typeCast( $values );
        }

        return $this;
    }

    /**
     * Apply type casts if feasible. If an invalid type is specified,
     * simply do nothing but keep the value.
     *
     * @param  array  $values
     *
     * @return array
     */
    protected function typeCast( array $values ) : array
    {
        $result = [];
        $keys   = array_column( $this->fields, 'name' );
        $casts  = array_column( $this->fields, 'type', 'name' );
        foreach ( $keys as $key ) {
            $value = $values[ $key ] ?? null;
            if ( ! is_null( $value ) && array_key_exists( $key, $casts ) ) {
                @settype( $value, $casts[ $key ] );
            }
            $result[ $key ] = $value;
        }

        return $result;
    }

    /**
     * Get the model's table
     *
     * @return string
     */
    public function getTable() : string
    {
        return $this->table;
    }

    /**
     * Get the table auto increment key. Default is "id"
     *
     * @return string
     */
    public function getAutoIncrement() : string
    {
        return 'id';
    }

    /**
     * @inheritDoc
     */
    public function toArray() : array
    {
        $keys = array_flip( array_column( $this->fields, 'name' ) );

        return array_intersect_key( $this->model_values, $keys );
    }

    /**
     * @inheritDoc
     */
    public function toJson() : string
    {
        return json_encode( $this->toArray() );
    }

    /**
     * IteratorAggregate - getIterator
     * Allows to treat the object as an array.
     * This object is loop-able.
     *
     * @return \ArrayIterator
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator( (array) $this->model_values );
    }

    /**
     * Magic setter
     *
     * @param $key
     * @param $value
     *
     * @return void
     */
    public function __set( $key, $value ) : void
    {
        $this->model_values[ $key ] = $value;
    }

    /**
     * Magic getter
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get( $key )
    {
        return $this->model_values[ $key ];
    }

}