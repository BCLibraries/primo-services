<?php

namespace BCLib\PrimoServices;

trait Accessor
{
    public function __get($property)
    {
        $method_name = 'get_' . $property;
        $property_name = '_' . $property;
        if (method_exists($this, $method_name))
        {
            return $this->$method_name();
        }
        elseif (property_exists($this, $property_name))
        {
            return $this->$property_name;
        }
        else
        {
            throw new \Exception($property . ' is not a property of ' . get_class());
        }
    }

    public function __set($property, $value)
    {
        $method_name = '_set_' . $property;
        $property_name = '_' . $property;
        if (method_exists($this, $method_name))
        {
            $this->$method_name($value);
        }
        elseif (property_exists($this, $property_name))
        {
            $this->$property_name = $value;
        }
        else
        {
            throw new \Exception($property . ' is not a property of ' . get_class());
        }
    }
}