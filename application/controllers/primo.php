<?php

require_once(__DIR__.'/XServiceRequest.php');
require_once 'HTTP/Request2.php';

class Primo extends XServiceRequest
{
     public function full_view($id)
     {
         $primo = new BCLib\XServices\Primo\FullView(new BCLib\XServices\Primo\FullViewArrayTranslator());
         $primo->setDocumentID($id);
         $output = $primo->send(new HTTP_Request2());
         print_r($output);
     }
}