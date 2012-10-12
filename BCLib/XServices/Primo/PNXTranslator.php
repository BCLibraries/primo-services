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
        $delivery_xml = $record_xml->delivery;

        $document = new \stdClass;
        $document->id = $this->_extractRecordID((string) $record_xml->control->recordid);
        $document->title = (string) $search_terms_xml->title;
        $document->creator = $this->_getCreator($record_xml);
        $document->contributors = $this->_getElementRange($facets_xml->creatorcontrib);
        $document->date = (string) $additional_data_xml->date;
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
        $document->genres = $this->_getElementRange($facets_xml->genre);
        $document->languages = $this->_getElementRange($facets_xml->language);
        $document->table_of_contents = $this->_getTableOfContents($search_terms_xml->toc);

        foreach ($display_data_xml->lds11 as $mms_id)
        {
            $document->mms = $mms_id;
        }

        $document->holdings = array();

        foreach ($display_data_xml->availlibrary as $available_library)
        {
            $document->holdings[] = $this->_extractHolding($available_library);
        }

        foreach ($delivery_xml->delcategory as $delcategory)
        {
            $document->delivery_category[] = $delcategory;
        }


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
        list(, $institution, $library, $location, $call_number, $availability) = preg_split('/\$\$./', (string) $available_library);
        return array('library' => $library,
            'location' => $location,
            'call_number' => substr($call_number, 1, -1),
            'availability' => $availability
        );
    }

}