<?php

namespace BCLib\PrimoServices\Availability;

interface AvailibilityClient
{
    /**
     * @param \BCLib\PrimoServices\BibRecord[] $results
     * @return \BCLib\PrimoServices\BibRecord[]
     */
    public function checkAvailability(array $results);
} 