<?php

namespace BCLib\XServices\Primo;

class PrimoRequestFactory
{
    const HOST = 'primo2.prod.alma.hosted.exlibrisgroup.com';
    const PORT = '1701';

    static function buildFullViewRequest($document_id,
                                         $host = PrimoRequestFactory::HOST,
                                         $port = PrimoRequestFactory::PORT)
    {
        $pnx_transaltor = new PNXTranslator();
        $full_view_translator = new FullViewObjectTranslator($pnx_transaltor);
        $request = new FullView($full_view_translator, $host, $port);
        $request->setDocumentID($document_id);
        $request->setInstitution('BCL');
        return $request;
    }

    static function buildBriefSearchRequest($institution = 'BCL',
                                            $bulk_size = 10,
                                            $start_index = 1,
                                            $host = PrimoRequestFactory::HOST,
                                            $port = PrimoRequestFactory::PORT)
    {
        $pnx_transaltor = new PNXTranslator();
        $brief_view_translator = new BriefSearchTranslator($pnx_transaltor);
        $request = new BriefSearch($brief_view_translator, $host, $port);
        $request->setInstitution($institution);
        $request->setPaging($bulk_size, $start_index);
        return $request;
    }

}
