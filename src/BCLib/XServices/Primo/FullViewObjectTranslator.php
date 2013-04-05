<?php

namespace BCLib\XServices\Primo;

class FullViewObjectTranslator implements \BCLib\XServices\Translator
{

    /** @var \BCLib\XServices\Primo\PNXTranslator * */
    private $_pnx_translator;

    public function __construct(PNXTranslator $pnx_translator = NULL)
    {
        if (is_null($pnx_translator))
        {
            $this->_pnx_translator = new PNXTranslator();
        }
        else
        {
            $this->_pnx_translator = $pnx_translator;
        }
    }

    public function translate(\SimpleXMLElement $xml)
    {
        $result = $this->_pnx_translator->translate($xml);
        return $result;
    }

}
