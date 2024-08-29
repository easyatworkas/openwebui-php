<?php

namespace EasyAtWork\OpenWebUi\Traits;

trait StaticConstructor
{
    /**
     * @see __construct()
     * @param ...$arguments
     * @return static
     */
    public static function create(... $arguments)
    {
        return new static(... $arguments);
    }
}
