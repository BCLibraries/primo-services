<?php

namespace BCLib\PrimoServices;

class BibComponent
{
    /**
     * @var array
     */
    public $alma_ids = [];

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
    public $availability = [];
}