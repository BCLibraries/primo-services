<?php

namespace BCLib\XServices\Primo;

class PNXTranslator
{

    private function _extractDoc(\SimpleXMLElement $doc_xml)
    {
        $xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');

        $document = new \stdClass;
        $document->id = (string) $doc_xml->PrimoNMBib->record->control->sourcerecordid;
        $document->title = (string) $doc_xml->PrimoNMBib->record->search->title;
        $document->abstract = (string) $doc_xml->PrimoNMBib->record->addata->abstract;
        $document->call_number = (string) $doc_xml->PrimoNMBib->record->display->lds07;
        $document->digitized = $this->_isDigitized($doc_xml);
        $document->availability = (string) $doc_xml->PrimoNMBib->record->display->availpnx;
        return $document;
    }

}