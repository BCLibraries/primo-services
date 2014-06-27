<?php

namespace BCLib\PrimoServices;

class PNXTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PNXTranslator */
    protected $_translator;

    public function setUp()
    {
        $this->_translator = new PNXTranslator();
    }

    public function testRecordIsLoaded()
    {
        $docset = $this->_loadTestRecord(__DIR__ . '/../../helpers/brief-search-result-local-01.json');

        $result = $this->_translator->translateDocSet($docset);
        $this->assertEquals(9, sizeof($result));

    }

    protected function _loadTestRecord($path_to_sample)
    {
        $json = json_decode(file_get_contents($path_to_sample));
        return $json->{'sear:SEGMENTS'}->{'sear:JAGROOT'}->{'sear:RESULT'}->{'sear:DOCSET'};
    }
}
 