<?php

namespace App\Helper;

trait PropertyAccessTrait
{
    public function __get(string $propertyName)
    {
        $methodName = 'get' . ucfirst($propertyName);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return $this->$propertyName;
    }
}
