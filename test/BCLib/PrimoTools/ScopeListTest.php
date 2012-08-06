<?php
namespace BCLib\PrimoTools;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-08-06 at 10:51:03.
 */
class ScopeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ScopeList
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = ScopeList::instantiate();
    }

    public function testInstantiateReturnsScopeList()
    {
        $this->assertInstanceOf('\BCLib\PrimoTools\ScopeList', $this->object);
    }

    public function testGetScopeReturnsCorrectScopes()
    {
        $expected_scopes = array();

        $expected_scopes['bc'] = new Scope('BCL', 'bc');
        $expected_scopes['archive'] = new Scope('ARCH', 'archive');
        $expected_scopes['burns'] = new Scope('BURNS', 'burns');
        $expected_scopes['bapst'] = new Scope('BAPST', 'bapst');
        $expected_scopes['gov'] = new Scope('GOV', 'gov');
        $expected_scopes['weston'] = new Scope('GEO', 'weston');
        $expected_scopes['media'] = new Scope('MEDIA', 'media');
        $expected_scopes['onl'] = new Scope('ONL', 'onl');
        $expected_scopes['nedl'] = new Scope('NEDL', 'nedl');
        $expected_scopes['law'] = new Scope('LAW', 'law');
        $expected_scopes['pci'] = new Scope('pci', 'pci');
        $expected_scopes['swk'] = new Scope('SWK', 'swk');
        $expected_scopes['tml'] = new Scope('TML','tml');
        $expected_scopes['icpsr'] = new Scope('bc_icpsr','icpsr');
        $expected_scopes['erc'] = new Scope('ERC', 'erc');
        $expected_scopes['stjc'] = new Scope('STJN','stjn');

        foreach ($expected_scopes as $name=>$expected_scope)
        {
            $this->assertEquals($expected_scope, $this->object->getScope($name));
        }
    }
}
