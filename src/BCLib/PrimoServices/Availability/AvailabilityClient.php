<?php

namespace BCLib\PrimoServices\Availability;

interface AvailabilityClient
{
    /**
     * @param \BCLib\PrimoServices\BibRecord[] $results
     * @return \BCLib\PrimoServices\BibRecord[]
     */
    public function checkAvailability(array $results);
} 