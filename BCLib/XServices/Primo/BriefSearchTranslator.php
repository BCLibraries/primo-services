<?php

namespace BCLib\XServices\Primo;

class BriefSearchTranslator implements \BCLib\XServices\Translator
{

    public function translate(\SimpleXMLElement $xml)
    {
        $result = array();
        $xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $docs = $xml->xpath('/sear:SEGMENTS/sear:JAGROOT/sear:RESULT/sear:DOCSET/sear:DOC');
        $result = \array_map(array($this, '_extractDoc'), $docs);
        return $result;
    }

    private function _extractDoc(\SimpleXMLElement $xml_document)
    {
        $result_document = new \stdClass();
        echo 'foo';
        $result_document->id = (string) $xml_document->PrimoNMBib->record->control->sourcerecordid;
        $result_document->title = (string) $xml_document->PrimoNMBib->record->search->title;
        $result_document->abstract = (string) $xml_document->PrimoNMBib->record->addata->abstract;
        $result_document->call_number = (string) $xml_document->PrimoNMBib->record->display->lds07;
        if ($this->_isDigitized($xml_document))
        {
            $result_document->digitized = TRUE;
        }
        $result_document->availability = (string) $xml_document->PrimoNMBib->record->display->availpnx;
        return $result_document;
    }

    private function _isDigitized(\SimpleXMLElement $xml_document)
    {
        return $xml_document->PrimoNMBib->record->display->lds08 == 'Streaming video';
    }
    
}