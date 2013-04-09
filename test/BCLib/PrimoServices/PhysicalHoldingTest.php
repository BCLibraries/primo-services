<?php

namespace BCLib\PrimoServices;

class PhysicalHoldingTest extends \PHPUnit_Framework_TestCase
{
    /** @var PhysicalHolding */
    protected $_holding;

    public function setUp()
    {
        $this->_holding = new PhysicalHolding();
    }

    /**
     * @expectedException \Exception
     */
    public function testSetInvalidLibraryThrowsException()
    {
        $this->_holding->library = 'not-a-library';
    }

    public function testValidLibrariesDontThrowException()
    {
        $valid_libraries = array('ONL', 'ERC', 'Burns');
        foreach ($valid_libraries as $valid_library)
        {
            $this->_holding->library = $valid_library;
        }
        $this->assertTrue(true);
    }

}
