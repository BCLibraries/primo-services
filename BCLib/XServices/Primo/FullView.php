<?php

namespace BCLib\XServices\Primo;

use BCLib\XServices;

class FullView extends PrimoRequest
{
    
    public function __construct(XServices\Translator $translator)
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/full');
    }
    
    public function docId($doc_id)
    {
        $this->_addArgument('docId', 'bc_aleph'.$doc_id);
    }
}