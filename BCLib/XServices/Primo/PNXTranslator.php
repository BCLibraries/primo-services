<?php

namespace BCLib\XServices\Primo;

class PNXTranslator
{

    public function translate(\SimpleXMLElement $doc_xml)
    {
        $result = new \stdClass;
        $doc_xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $doc_xml->registerXPathNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');
        $docs_xml = $doc_xml->xpath('//sear:DOC/PrimoNMBib/record|//sear:DOC/prim:PrimoNMBib/prim:record');
        $result->items = \array_map(array($this, '_extractDoc'), $docs_xml);
        return $result;
    }

    private function _extractDoc(\SimpleXMLElement $record_xml)
    {
        $record_xml = $record_xml->children('http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');

        $facets_xml = $record_xml->facets;
        $display_data_xml = $record_xml->display;
        $additional_data_xml = $record_xml->addata;
        $search_terms_xml = $record_xml->search;
        $delivery_xml = $record_xml->delivery;

        $document = new \stdClass;
        $document->id = $this->_extractRecordID((string) $record_xml->control->recordid);
        $document->title = (string) $display_data_xml->title;
        $document->creator = $this->_getCreator($record_xml);
        $document->contributors = $this->_getElementRange($display_data_xml->contributor);
        $document->date = (string) $display_data_xml->creationdate;
        $document->abstract = (string) $additional_data_xml->abstract;
        $document->frbr_group_id = (string) $facets_xml->frbrgroupid;
        $document->type = (string) $display_data_xml->type;
        $document->url = $this->_getURL($record_xml);
        $document->availability = (string) $display_data_xml->availpnx;
        $document->cover_images = $this->_getCoverImages($record_xml);
        $document->isbn = (string) $search_terms_xml->isbn;
        $document->issn = (string) $search_terms_xml->issn;
        $document->oclcid = (string) $additional_data_xml->oclcid;
        $document->reserves_info = (string) $additional_data_xml->lad05;
        $document->subjects = $this->_getElementRange($search_terms_xml->subject);
        $document->subjects['display'] = (string) $display_data_xml->subject;
        $document->genres = $this->_getElementRange($facets_xml->genre);
        $document->languages = $this->_getElementRange($display_data_xml->language);
        $document->table_of_contents = $this->_getTableOfContents($search_terms_xml->toc);
        $document->format = (string) $display_data_xml->format;
        $document->description = $this->_getElementRange($display_data_xml->description);

        $deep_link = new \BCLib\DeepLinks\FullView($document->id);
        $document->permalink = (string) $deep_link;

        foreach ($display_data_xml->lds11 as $mms_id)
        {
            $document->mms = (string) $mms_id;
        }

        $document->holdings = $this->_extractHoldings($record_xml);

        foreach ($delivery_xml->delcategory as $delcategory)
        {
            $document->delivery_category[] = (string) $delcategory;
        }


        return $document;
    }

    private function _extractHoldings(\SimpleXMLElement $record_xml)
    {
        $holdings = array();

        foreach ($record_xml->display->availlibrary as $available_library)
        {
            $holdings[] = $this->_extractHolding($available_library);
        }

        return $holdings;
    }

    private function _getCoverImages(\SimpleXMLElement $record_xml)
    {
        $sizes = array('small' => 'sc', 'medium' => 'mc', 'large' => 'lc');

        $cover_images = new \stdClass;

        if (isset($record_xml->links->thumbnail) && isset($record_xml->search->isbn))
        {
            $image_base_url = 'http://lib.syndetics.com/index.aspx?client=bostonh&isbn=';
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
        $result->display_name = (string) $record_xml->display->creator;
        $result->authority_name = (string) $record_xml->addata->addau;
        $result->last_name = (string) $record_xml->addata->aulast;
        $result->first_name = (string) $record_xml->addata->aufirst;
        return $result;
    }

    private function _getURL(\SimpleXMLElement $record_xml)
    {
        if ((string) $record_xml->display->lds08 == 'Streaming video')
        {
            return 'http://mlib.bc.edu/media/clip/' . (string) $record_xml->control->sourcerecordid;
        }
        else
        {
            if (isset($record_xml->links->linktorsrc))
            {
                return $this->_extractLinkToResource((string) $record_xml->links->linktorsrc);
            }
        }
    }

    private function _getTableOfContents(\SimpleXMLElement $toc_xml)
    {
        return explode(' -- ', (string) $toc_xml);
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

    private function _extractLinkToResource($resource_link_macro)
    {
        $extract_url_regex = '/\$\$U(.*)\$\$D/';
        preg_match($extract_url_regex, $resource_link_macro, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }

    private function _extractRecordID($record_id_string)
    {
        return str_replace('bc_aleph', '', $record_id_string);
    }

    private function _extractHolding(\SimpleXMLElement $available_library)
    {
        $availability = preg_split('/\$\$./', (string) $available_library);
        $library = isset($availability[2]) ? $availability[1] : '';
        $location = isset($availability[3]) ? $availability[2] : '';
        $call_number = isset($availability[4]) ? $availability[3] : '';
        $availability = isset($availability[5]) ? $availability[4] : '';

        return array('library' => $library,
            'location' => $location,
            'call_number' => substr($call_number, 1, -1),
            'availability' => $availability
        );
    }

}