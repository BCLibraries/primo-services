<?php

require_once(__DIR__.'/XServiceRequest.php');
require_once 'HTTP/Request2.php';

use BCLib\XServices;

class Primo extends XServiceRequest
{
     public function full_view($id)
     {
         $primo = XServices\Primo\PrimoRequestFactory::buildFullViewRequest($id,'libsearch.bc.edu','80');
         $output = $primo->send(new HTTP_Request2());
         $this->output->set_output(json_encode($output));
     }

    public function brief_view($query)
    {
        $primo = XServices\Primo\PrimoRequestFactory::buildBriefSearchRequest('BCL',10,1,'libsearch.bc.edu','80');
        $primo->setKeyword($query);
        $output = $primo->send(new HTTP_Request2());
        $this->output->set_output(json_encode($output));
    }
}