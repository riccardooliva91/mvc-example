<?php

namespace App\Models;

/**
 * Class Team
 *
 * @package App\Models
 */
class Team extends BaseModel
{

    /**
     * @var array
     */
    protected $fields = [
        [ 'name' => 'id', 'type' => 'integer' ],
        [ 'name' => 'name', 'type' => 'string' ],
        [ 'name' => 'foundation_date', 'type' => 'string' ],
        [ 'name' => 'registered_office', 'type' => 'string' ],
    ];

    /**
     * @var string
     */
    protected $table = 'teams';

}