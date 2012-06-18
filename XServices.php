<?php

require 'SplClassLoader/SplClassLoader.php';
$dm_loader = new SplClassLoader('BCLib\XServices', __DIR__);
$dm_loader->register();