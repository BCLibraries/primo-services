<?php

namespace BCLib\XServices\Primo;

class FullViewTranslator implements \BCLib\XServices\Translator
{

    public function translate(\SimpleXMLElement $xml)
    {
        $result = array();

        $xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        foreach ($xml->xpath('//sear:DOC') as $doc)
        {
            $result['title'] = (string) $doc->PrimoNMBib->record->search->title;
            $result['abstract'] = (string) $doc->PrimoNMBib->record->addata->abstract;
            $result['id'] = (string) $doc->PrimoNMBib->record->control->sourcerecordid;
            $result['call-number'] = (string) $doc->PrimoNMBib->record->display->lds07;
            $result['largethumb'] = $this->_getLargeImage($doc);
        }

        return $result;
    }

    private function _getLargeImage($doc)
    {
        if (isset($doc->PrimoNMBib->record->search->isbn))
        {
            $image_url = 'http://lib.syndetics.com/index.aspx?client=bostonh&amp;isbn=';
            $image_url .= (string) $doc->PrimoNMBib->record->search->isbn . '/LC.JPG';
        }
        else
        {
            $image_url = '/video-search/_SupportFiles/_Images/physical-video-large.png';
        }
        return $image_url;
    }

}