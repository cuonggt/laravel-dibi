<?php

namespace Cuonggt\Dibi;

abstract class DBObject
{
    /**
     * The DB object's raw attributes.
     *
     * @var array
     */
    public $raw;

    /**
     * Get the raw DB object array.
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Set the raw DB object array from the provider.
     *
     * @return $this
     */
    public function setRaw(array $raw)
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Map the given array onto the DB object's properties.
     *
     * @return $this
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
