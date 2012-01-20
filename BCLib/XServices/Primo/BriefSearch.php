<?php

namespace BCLib\XServices\Primo;

class BriefSearch extends PrimoRequest
{

    public function __construct(XServices\BriefSearchTranslator $translator)
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/brief');
    }
}