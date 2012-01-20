<?php

require 'SplClassLoader/SplClassLoader.php';
$x_services_loader = new SplClassLoader('BCLib', __DIR__);
$x_services_loader->register();