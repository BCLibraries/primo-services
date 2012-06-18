<?php

require_once(__DIR__.'/XServiceRequest.php');
require_once 'HTTP/Request2.php';

use BCLib\XServices;

class Primo extends XServiceRequest
{
     public function full_view($id)
     {
         $primo = XServices\Primo\PrimoRequestFactory::buildFullViewRequest($id,'agama.bc.edu');
         $output = $primo->send(new HTTP_Request2());
         print_r($output);
     }

    public function brief_view($query)
    {
        $primo = XServices\Primo\PrimoRequestFactory::buildBriefSearchRequest('BCL',10,1,'agama.bc.edu');
        $primo->setKeyword('foo');
        $output = $primo->send(new HTTP_Request2());
        print_r($output);
    }
}