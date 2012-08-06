<?php

namespace BCLib\PrimoTools;

class Scope
{
    private $_primo_id;
    private $_path_id;

    public function __construct($primo_id, $path_id)
    {
        $this->_primo_id = $primo_id;
        $this->_path_id = $path_id;
    }

    public function getPrimoID()
    {
        return $this->_primo_id;
    }

    public function getPathID()
    {
        return $this->_path_id;
    }
}
