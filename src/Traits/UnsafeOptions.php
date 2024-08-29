<?php

namespace EasyAtWork\OpenWebUi\Traits;

trait UnsafeOptions
{
    /**
     * @param array $options
     * @return void
     */
    protected function absorbOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
