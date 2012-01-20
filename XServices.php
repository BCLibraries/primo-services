<?php

require 'SplClassLoader/SplClassLoader.php';
$x_services_loader = new SplClassLoader('BCLib\DigitalMeasures', __DIR__.'/..');
$x_services_loader->register();

$general_loader = new SplClassLoader();
$general_loader->register();