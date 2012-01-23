<?php

namespace BCLib\XServices\Primo;

class BriefSearch extends PrimoRequest
{

    public function __construct(BriefSearchTranslator $translator)
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/brief');
    }

    public function requestFrbrGroup($frbr_group_id, $bulk_size = 1000, $start_index = 1)
    {
        $this->setInstitution('BCL');
        $this->_addQuery('facet_frbrgroupid', 'exact', $frbr_group_id);
        $this->_addArgument('bulkSize', $bulk_size);
        $this->_addArgument('indx', $start_index);
    }

}