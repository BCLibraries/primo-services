<?php

namespace BCLib\DeepLinks;

class FullView extends DeepLink
{
    public function __construct($id, $host = 'bc-primo.hosted.exlibrisgroup.com', $port = '0')
    {
        $this->_setURL('dlDisplay.do', $host, $port);
        $this->_query_string_fields[] = 'institution=BCL&vid=bclib&onCampus=true&group=GUEST';
        $this->_query_string_fields[] = 'docId=' . $id;
    }


}
