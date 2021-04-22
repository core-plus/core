<?php

namespace Core\Models\Concerns;

trait Macroable
{
    use \Illuminate\Support\Traits\Macroable {
        __call as macroCall;
    }

    /**
     * Get a relationship value from a method.
     *
     * @param string $key
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function getRelationValue($key)
    {
        $relation = parent::getRelationValue($key);
        if (! $relation && static::hasMacro($key)) {
            return $this->getRelationshipFromMethod($key);
        }

        return $relation;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return parent::__callStatic($method, $parameters);
    }
}
