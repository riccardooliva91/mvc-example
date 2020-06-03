<?php

namespace App\Models;

/**
 * Class Racer
 *
 * @package App\Models
 */
class Racer extends BaseModel
{

    /**
     * @var array
     */
    protected $fields = [
        [ 'name' => 'id', 'type' => 'integer' ],
        [ 'name' => 'name', 'type' => 'string' ],
        [ 'name' => 'surname', 'type' => 'string' ],
        [ 'name' => 'country', 'type' => 'string' ],
        [ 'name' => 'team_id', 'type' => 'integer' ],
        [ 'name' => 'birth_date', 'type' => 'string' ],
    ];

    /**
     * @var string
     */
    protected $table = 'racers';

}