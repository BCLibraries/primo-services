<?php

namespace BCLib\PrimoServices;

class BibComponent
{
    /**
     * @var string
     */
    public $alma_ids = array();

    /**
     * @var string
     */
    public $source;

    /**
     * @var string
     */
    public $source_record_id;

    /**
     * @var string
     */
    public $delivery_category;

    /**
     * @var \BCLib\PrimoServices\Availability\Availability[]
     */
    public $availability = array();
}