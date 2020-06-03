<?php

namespace App\Interfaces;

/**
 * Interface Arrayable
 *
 * @package App\Interfaces
 */
interface Arrayable
{

    /**
     * Get array representation of the object
     *
     * @return array
     */
    public function toArray() : array;

}