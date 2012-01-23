<?php

namespace BCLib\XServices\Primo;

class BriefSearchTranslator implements \BCLib\XServices\Translator
{
    
    public function translate(\SimpleXMLElement $xml)
    {
        $result = new \stdClass();
        $xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $facets_xml = $xml->xpath('/sear:SEGMENTS/sear:JAGROOT/sear:RESULT/sear:FACETLIST/sear:FACET');
        $result->facets = \array_map(array($this, '_extractFacet'), $facets_xml);

        $docs_xml = $xml->xpath('/sear:SEGMENTS/sear:JAGROOT/sear:RESULT/sear:DOCSET/sear:DOC');
        $result->docs = \array_map(array($this, '_extractDoc'), $docs_xml);
        return $result;
    }

    /**
     * Extracts a single facet
     * 
     * @param \SimpleXMLElement $facet_xml
     * @return \stdClass 
     */
    private function _extractFacet(\SimpleXMLElement $facet_xml)
    {
        $facet = new \stdClass();
        $facet->name = (string) $facet_xml['NAME'];
        foreach ($facet_xml->children('sear', true) as $facet_value_xml)
        {
            $facet->values[] = $this->_extractFacetValue($facet_value_xml);
        }
        return $facet;
    }

    /**
     * Extracts a single facet value
     * 
     * @param \SimpleXMLElement $facet_value_xml
     * @return \stdClass 
     */
    private function _extractFacetValue(\SimpleXMLElement $facet_value_xml)
    {
        $facet_value = new \stdClass();
        foreach ($facet_value_xml->attributes() as $key => $value)
        {
            switch ($key)
            {
                case 'KEY':
                    $facet_value->value = (string) $value;
                    break;
                case 'VALUE':
                    $facet_value->count = (string) $value;
            }
        }
        return $facet_value;
    }

    /**
     * Extrancts a single document
     * 
     * @param \SimpleXMLElement $doc_xml
     * @return \stdClass 
     */
    private function _extractDoc(\SimpleXMLElement $doc_xml)
    {
        $document = new \stdClass();
        $document->id = (string) $doc_xml->PrimoNMBib->record->control->sourcerecordid;
        $document->title = (string) $doc_xml->PrimoNMBib->record->search->title;
        $document->abstract = (string) $doc_xml->PrimoNMBib->record->addata->abstract;
        $document->call_number = (string) $doc_xml->PrimoNMBib->record->display->lds07;
        $document->digitized = $this->_isDigitized($doc_xml);
        $document->availability = (string) $doc_xml->PrimoNMBib->record->display->availpnx;
        return $document;
    }

    /**
     * Returns true if the items has been digitized
     * 
     * @param \SimpleXMLElement $xml_document
     * @return boolean 
     */
    private function _isDigitized(\SimpleXMLElement $xml_document)
    {
        return $xml_document->PrimoNMBib->record->display->lds08 == 'Streaming video';
    }

}