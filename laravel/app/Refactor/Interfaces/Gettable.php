<?php

namespace App\Refactor\Interfaces;

/**
 * Forces any classes to adhere i.e classes must have a get method.
 */
interface Gettable
{
    /**
     * Get the result
     *
     * @return mixed
     */
    public function get();
}
