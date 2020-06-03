<?php

namespace App\Models;

/**
 * Class Ranking
 *
 * @package App\Models
 */
class Ranking extends BaseModel
{

    /**
     * @var array
     */
    protected $fields = [
        [ 'name' => 'id', 'type' => 'integer' ],
        [ 'name' => 'race_id', 'type' => 'integer' ],
        [ 'name' => 'racer_id', 'type' => 'integer' ],
        [ 'name' => 'rank', 'type' => 'integer' ],
    ];

    /**
     * @var string
     */
    protected $table = 'rankings';

}