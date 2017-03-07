<?php

namespace BCLib\PrimoServices;

class BibComponentTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var  BibComponentTranslator */
    protected $_translator;

    public function setUp()
    {
        $this->_translator = new BibComponentTranslator();
    }

    public function testTranslatingSingleRecordReturnsArray()
    {
        $component = $this->_loadTestRecord(__DIR__ . '/../../helpers/4.7/bib-component-01.json');
        $result = $this->_translator->translate($component);
        $this->assertTrue(is_array($result));
    }

    public function testTranslatingDedupedRecordReturnsArray()
    {
        $component = $this->_loadTestRecord(__DIR__ . '/../../helpers/4.7/bib-component-02.json');
        $result = $this->_translator->translate($component);
        $this->assertTrue(is_array($result));    }

    protected function _loadTestRecord($path_to_sample)
    {
        return json_decode(file_get_contents($path_to_sample));
    }
}