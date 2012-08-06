<?php
namespace BCLib\PrimoTools;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-08-06 at 10:49:49.
 */
class ScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Scope
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Scope('ONL','onl');
    }

    public function testGetPrimoIDGetsID()
    {
        $this->assertEquals('ONL',$this->object->getPrimoID());
    }
}