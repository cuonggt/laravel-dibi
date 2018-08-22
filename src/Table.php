<?php

namespace Cuonggt\Dibi;

use ArrayAccess;
use Illuminate\Support\Facades\DB;

class Table implements ArrayAccess
{
    /**
     * The table's raw attributes.
     *
     * @var array
     */
    public $table;

    /**
     * Get the raw table array.
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->table;
    }

    /**
     * Set the raw table array from the provider.
     *
     * @param  array  $table
     * @return $this
     */
    public function setRaw(array $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Map the given array onto the table's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Determine if the given raw table attribute exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->table);
    }

    /**
     * Get the given key from the raw table.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->table[$offset];
    }

    /**
     * Set the given attribute on the raw table array.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->table[$offset] = $value;
    }

    /**
     * Unset the given value from the raw table array.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->table[$offset]);
    }
}
