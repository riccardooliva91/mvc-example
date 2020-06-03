<?php

namespace App\Interfaces;

/**
 * Interface Jsonable
 *
 * @package App\Interfaces
 */
interface Jsonable
{

    /**
     * Get json representation of the object
     *
     * @return string
     */
    public function toJson() : string;

}