<?php

namespace BCLib\XServices\Primo;

class AvailabilityTranslator
{
    public function translate(\SimpleXMLElement $record_xml)
    {
        $availablity = array();
        foreach ($record_xml->delivery->delcategory as $delcategory)
        {

        }
    }
}
