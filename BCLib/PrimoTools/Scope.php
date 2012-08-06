<?php

namespace BCLib\PrimoTools;

class Scope
{
    private $_primo_id;
    private $_is_local;

    public function __construct($primo_id, $is_local = TRUE)
    {
        $this->_primo_id = $primo_id;
        $this->_is_local = $is_local;
    }

    public function __toString()
    {
        if ($this->_is_local)
        {
            return $this->_strigifyLocalScope();
        }
        else
        {
            return $this->_stringifyRemoteScope();
        }
    }

    private function _strigifyLocalScope()
    {
        return 'loc=local,scope:(' . $this->_primo_id . ')';
    }

    private function _stringifyRemoteScope()
    {
        return 'loc=adaptor,primo_central_multiple_fe&tab=pci_only';
    }
}
