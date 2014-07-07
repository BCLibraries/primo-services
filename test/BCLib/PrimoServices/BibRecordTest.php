<?php

namespace BCLib\PrimoServices;

class BibRecordTest extends \PHPUnit_Framework_TestCase
{
    public function testAddCustomFieldWorks()
    {
        $bib = new BibRecord();
        $bib->addField('display','lds01','foobar');
        $this->assertEquals('foobar', $bib->field('display/lds01'));
    }
}