<?php

// Prepends include_path. You could alternately do this via .htaccess or php.ini
set_include_path(dirname(__FILE__) . '/lib'.PATH_SEPARATOR.get_include_path());

require 'Minify.php';
Minify::setCache(); // in 2.0 was "useServerCache"
Minify::serve('Groups', array(
    'groups' => (require 'groupsConfig.php'),'maxAge' => 31536000, 'encodeMethod' => "gzip"
));