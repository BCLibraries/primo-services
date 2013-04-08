<?php

namespace BCLib\PrimoServices;

class PhysicalHolding extends Holding implements \JsonSerializable
{

    protected $_library;

    public function __construct()
    {
        $this->_type = 'physical';
    }

    protected function _set_library($library)
    {
        $valid_libraries = array('ONL', 'ERC', 'Burns');
        if (!in_array($library, $valid_libraries))
        {
            throw new \Exception($library . ' is not a valid library name');
        }
        $this->_library = $library;
    }


}