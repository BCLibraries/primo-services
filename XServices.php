<?php

require 'SplClassLoader/SplClassLoader.php';
$dm_loader = new SplClassLoader('BCLib', __DIR__);
$dm_loader->register();