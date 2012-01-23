<?php

require_once 'HTTP/Request2.php';
require_once 'SplClassLoader/SplClassLoader.php';
$x_services_loader = new SplClassLoader('BCLib', __DIR__);
$x_services_loader->register();