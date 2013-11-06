<?php

namespace BCLib\PrimoServices;

class PNXTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PNXTranslator */
    protected $_translator;

    public function setUp()
    {
        $bib_record = \Mockery::mock('BCLib\PrimoServices\BibRecord');
        $bib_record->shouldReceive('load');
        $this->_translator = new PNXTranslator($bib_record);
    }

    public function testRecordIsLoaded()
    {
        $xml = $this->_loadTestRecord(__DIR__ . '/../../helpers/brief-search-result-local-01.xml');
        $result = $this->_translator->translate($xml);
        $this->assertEquals(9, sizeof($result));

    }

    protected function _loadTestRecord($path_to_sample)
    {
        return simplexml_load_file($path_to_sample);
    }
}
 