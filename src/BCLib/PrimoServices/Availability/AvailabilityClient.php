<?php

namespace BCLib\PrimoServices\Availability;

interface AvailabilityClient
{
    /**
     * Add availability information to an array of BibRecords
     *
     * @param \BCLib\PrimoServices\BibRecord[] $bib_records
     * @return \BCLib\PrimoServices\BibRecord[]
     */
    public function checkAvailability(array $bib_records);
} 