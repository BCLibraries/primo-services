<?php

namespace BCLib\PrimoServices;

class BibRecordTest extends \PHPUnit_Framework_TestCase
{
    /** @var BibRecord */
    protected $_bib_record;

    public function setUp()
    {
        $this->_bib_record = new BibRecord();
    }

    public function testEmptyTest()
    {
        return $this->assertTrue(true);
    }
}
