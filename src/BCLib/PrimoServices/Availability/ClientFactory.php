<?php

namespace BCLib\PrimoServices\Availability;

class ClientFactory
{
    public function buildAlmaClient($alma_host, $library)
    {
        return new AlmaClient(null, $alma_host, $library);
    }
}
