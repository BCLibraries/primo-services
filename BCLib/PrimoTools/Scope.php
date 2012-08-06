<?php

namespace BCLib\PrimoTools;

class Scope
{
    private $_primo_id;

    public function __construct($primo_id)
    {
        $this->_primo_id = $primo_id;
    }

    public function getPrimoID()
    {
        return $this->_primo_id;
    }
}
