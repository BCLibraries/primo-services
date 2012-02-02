<?php

namespace BCLib\XServices\Primo;

class PrimoRequestFactory
{

    static function buildFullViewRequest($document_id)
    {
        $pnx_transaltor = new PNXTranslator();
        $full_view_translator = new FullViewObjectTranslator($pnx_transaltor);
        $request = new FullView($full_view_translator);
        $request->setDocumentID($document_id);
        return $request;
    }

    static function buildBriefSearchRequest($institution = 'BCL', $bulk_size = 10, $start_index = 1)
    {
        $pnx_transaltor = new PNXTranslator();
        $brief_view_translator = new BriefSearchTranslator($pnx_transaltor);
        $request = new BriefSearch($brief_view_translator);
        $request->setInstitution($institution);
        $request->setPaging($bulk_size, $start_index);
        return $request;
    }

}