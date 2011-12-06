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
            $result['largethumb'] = $this->_getImage($doc, 'large');
            $result['mediumthumb'] = $this->_getImage($doc, 'medium');
            $result['availability'] = (string) $doc->PrimoNMBib->record->display->availpnx;
        }

        return $result;
    }

    private function _getImage($doc, $size)
    {
        switch ($size)
        {
            case 'large':
                $thumb_class = 'lc';
                break;
            case 'medium':
                $thumb_class = 'mc';
                break;
            default:
                $thumb_class = 'sc';
        }
        if (isset($doc->PrimoNMBib->record->search->isbn))
        {
            $image_url = 'http://lib.syndetics.com/index.aspx?client=bccls&amp;isbn=';
            $image_url .= (string) $doc->PrimoNMBib->record->search->isbn . '/' . $thumb_class . '.JPG';
        } else
        {
            $image_url = FALSE;
        }
        return $image_url;
    }

}