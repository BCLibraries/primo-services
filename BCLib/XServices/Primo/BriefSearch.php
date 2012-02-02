<?php

namespace BCLib\XServices\Primo;

class BriefSearch extends PrimoRequest
{
    
    public function __construct(BriefSearchTranslator $translator)
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/brief');
    }

    public function setFrbrGroup($frbr_group_id, $bulk_size = 1000, $start_index = 1)
    {
        $this->_addQuery('facet_frbrgroupid', 'exact', $frbr_group_id);
    }

    public function setResourceType($resource_type)
    {
        $this->_addQuery('facet_rtype', 'exact', $resource_type);
    }

    public function setCreationDate($start_year, $end_year)
    {
        $date_string = '['.$start_year.'+TO+'.$end_year.']';
        $this->_addQuery('facet_creationdate', 'exact', "[$start_year+TO+$end_year]");
    }

    public function setPaging($bulk_size, $start_index)
    {
        $this->_addArgument('bulkSize', $bulk_size);
        $this->_addArgument('indx', $start_index);
    }

}