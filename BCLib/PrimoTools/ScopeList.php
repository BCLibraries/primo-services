<?php

namespace BCLib\PrimoTools;

class ScopeList
{
    /** @var Scope[] */
    private $_scopes = array();

    public static function instantiate()
    {
        return new ScopeList();
    }

    private function __construct()
    {
        $this->_scopes['bc'] = new Scope('BCL', 'bc');
        $this->_scopes['archive'] = new Scope('ARCH', 'archive');
        $this->_scopes['burns'] = new Scope('BURNS', 'burns');
        $this->_scopes['bapst'] = new Scope('BAPST', 'bapst');
        $this->_scopes['gov'] = new Scope('GOV', 'gov');
        $this->_scopes['weston'] = new Scope('GEO', 'weston');
        $this->_scopes['media'] = new Scope('MEDIA', 'media');
        $this->_scopes['onl'] = new Scope('ONL', 'onl');
        $this->_scopes['nedl'] = new Scope('NEDL', 'nedl');
        $this->_scopes['law'] = new Scope('LAW', 'law');
        $this->_scopes['pci'] = new Scope('pci', 'pci');
        $this->_scopes['swk'] = new Scope('SWK', 'swk');
        $this->_scopes['tml'] = new Scope('TML','tml');
        $this->_scopes['icpsr'] = new Scope('bc_icpsr','icpsr');
        $this->_scopes['erc'] = new Scope('ERC', 'erc');
        $this->_scopes['stjc'] = new Scope('STJN','stjn');

    }

    public function getScope($path_id)
    {
        return $this->_scopes[$path_id];
    }

    public function getValidPathIDs()
    {
        return \array_keys($this->_scopes);
    }
}
