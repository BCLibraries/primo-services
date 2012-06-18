<?php

require_once(__DIR__.'/XServiceRequest.php');
require_once 'HTTP/Request2.php';

class Primo extends XServiceRequest
{
     public function full_view($id)
     {
         $primo = \BCLib\XServices\Primo\PrimoRequestFactory::buildFullViewRequest($id,'agama.bc.edu');
         $output = $primo->send(new HTTP_Request2());
         print_r($output);
     }
}