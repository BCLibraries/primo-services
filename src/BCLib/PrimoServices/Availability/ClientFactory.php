<?php

namespace BCLib\PrimoServices\Availability;

use Guzzle\Http\Client;

class ClientFactory
{
    public function buildAlmaClient($alma_host, $library)
    {
        return new AlmaClient(new Client(), $alma_host, $library);
    }
}