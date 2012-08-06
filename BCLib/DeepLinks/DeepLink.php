<?php

namespace BCLib\DeepLinks;

abstract class DeepLink
{
    private $_host;
    private $_port;
    private $_command;

    protected $_query_string_fields = array();

    protected function _setURL($command, $host = 'bc-primo.hosted.exlibrisgroup.com', $port = '0')
    {
        $this->_host = $host;
        $this->_port = $port;
        $this->_command = $command;
    }

    public function __toString()
    {
        $port_string = $this->_port ? ':' . $this->_port : '';
        $base_url = 'http://' . $this->_host . $port_string . '/primo_library/libweb/action/' . $this->_command;
        $query_string = implode('&', $this->_query_string_fields);
        return $base_url . '?' . $query_string;
    }
}