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
        $this->_scopes['bc'] = new Scope('BCL');
        $this->_scopes['archive'] = new Scope('ARCH');
        $this->_scopes['burns'] = new Scope('BURNS');
        $this->_scopes['bapst'] = new Scope('BAPST');
        $this->_scopes['gov'] = new Scope('GOV');
        $this->_scopes['weston'] = new Scope('GEO');
        $this->_scopes['media'] = new Scope('MEDIA');
        $this->_scopes['onl'] = new Scope('ONL');
        $this->_scopes['nedl'] = new Scope('NEDL');
        $this->_scopes['law'] = new Scope('LAW');
        $this->_scopes['pci'] = new Scope('pci', false);
        $this->_scopes['swk'] = new Scope('SWK');
        $this->_scopes['tml'] = new Scope('TML');
        $this->_scopes['icpsr'] = new Scope('bc_icpsr');
        $this->_scopes['erc'] = new Scope('ERC');
        $this->_scopes['stjc'] = new Scope('STJN');

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
