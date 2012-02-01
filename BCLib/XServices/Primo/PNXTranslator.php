<?php

namespace BCLib\XServices\Primo;

class PNXTranslator
{

    public function translate(\SimpleXMLElement $doc_xml)
    {
        $result = new \stdClass;
        $doc_xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $docs_xml = $doc_xml->xpath('//sear:DOC');
        $result->items = \array_map(array($this, '_extractDoc'), $docs_xml);
        return $result;
    }

    private function _extractDoc(\SimpleXMLElement $record_xml)
    {
        $record_xml = $record_xml->PrimoNMBib->record;
        
        $facets_xml = $record_xml->facets;
        $display_data_xml = $record_xml->display;
        $additional_data_xml = $record_xml->addata;
        $search_terms_xml = $record_xml->search;
        
        
        $document = new \stdClass;
        $document->id = (string) $record_xml->control->sourcerecordid;
        $document->title = (string) $search_terms_xml->title;
        $document->creator = $this->_getCreator($record_xml);
        $document->contributors = $this->_getElementRange($facets_xml->creatorcontrib);
        $document->date = (string) $additional_data_xml->date;
        $document->abstract = (string) $additional_data_xml->abstract;
        $document->call_number = (string) $display_data_xml->lds07;
        $document->digitized = $this->_isDigitized($record_xml);
        $document->availability = (string) $display_data_xml->availpnx;
        $document->cover_images = $this->_getCoverImages($record_xml);
        $document->isbn = (string) $search_terms_xml->isbn;
        $document->subjects = $this->_getElementRange($search_terms_xml->subject);
        $document->genres = $this->_getElementRange($facets_xml->genre);
        $document->languages = $this->_getElementRange($facets_xml->language);
        $document->table_of_contents = $this->_getTableOfContents($search_terms_xml->toc);
        
        return $document;
    }

    private function _getCoverImages(\SimpleXMLElement $record_xml)
    {
        $sizes = array('small' => 'sc', 'medium' => 'mc', 'large' => 'lc');

        $cover_images = new \stdClass;

        if (isset($record_xml->links->thumbnail) && isset($record_xml->search->isbn))
        {
            $image_base_url = 'http://lib.syndetics.com/index.aspx?client=bccls&isbn=';
            $isbn = (string) $record_xml->search->isbn;

            foreach ($sizes as $size => $abbreviation)
            {
                $cover_images->$size = $image_base_url . $isbn . '/' . $abbreviation . '.JPG';
            }
        }
        return $cover_images;
    }

    private function _getCreator(\SimpleXMLElement $record_xml)
    {
        $result = new \stdClass;
        $result->authority_name = (string) $record_xml->addata->addau;
        $result->last_name = (string) $record_xml->addata->aulast;
        $result->first_name = (string) $record_xml->addata->aufirst;
        return $result;
    }

    /**
     * Returns true if the items has been digitized
     * 
     * @param \SimpleXMLElement $record_xml
     * @return boolean 
     */
    private function _isDigitized(\SimpleXMLElement $record_xml)
    {
        return  $record_xml->display->lds08 == 'Streaming video'
                || isset($record_xml->links->linktorsrc);
    }
    
    private function _getTableOfContents(\SimpleXMLElement $toc_xml)
    {
        return split(' -- ', (string) $toc_xml);
    }

    private function _getElementRange(\SimpleXMLElement $range_xml)
    {
        $result = array();
        foreach ($range_xml as $xml)
        {
            $result[] = (string) $xml;
        }
        return $result;
    }

}