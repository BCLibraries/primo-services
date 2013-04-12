<?php

namespace BCLib\PrimoServices;

trait EncodeJson
{
    /**
     * Encode JSON
     *
     * Send all non-null variables.
     *
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        $data = new \stdClass;

        foreach ($this as $key => $value)
        {
            $key = substr($key, 1);
            if (!is_null($value) && (!(is_array($value) && count($value) == 0)))
            {
                $data->$key = $value;
            }
        }

        return $data;
    }
}