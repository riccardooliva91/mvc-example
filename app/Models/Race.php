<?php

namespace App\Models;

/**
 * Class Race
 *
 * @package App\Models
 */
class Race extends BaseModel
{

    /**
     * @var array
     */
    protected $fields = [
        [ 'name' => 'id', 'type' => 'integer' ],
        [ 'name' => 'edition', 'type' => 'integer' ],
        [ 'name' => 'start', 'type' => 'string' ],
        [ 'name' => 'end', 'type' => 'string' ],
        [ 'name' => 'length', 'type' => 'integer' ],
        [ 'name' => 'height_difference', 'type' => 'integer' ],
        [ 'name' => 'difficulty', 'type' => 'integer' ],
    ];

    /**
     * @var string
     */
    protected $table = 'races';

}