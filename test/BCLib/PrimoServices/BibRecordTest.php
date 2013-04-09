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

    /**
     * @expectedException \Exception
     */
    public function testBadCoverImageThrowsException()
    {
        $this->_bib_record->addCoverImage('http://www.example.com/foo.png', 'not-a-size');
    }

    public function testValidCoverImageSizesDontThrowException()
    {
        $valid_sizes = array('small', 'medium', 'large');
        foreach ($valid_sizes as $valid_size)
        {
            $this->_bib_record->addCoverImage('http://www.example.com/foo.png', $valid_size);
        }
        $this->assertTrue(true);
    }
}
