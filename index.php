<?php
error_reporting(E_ALL &~ (E_STRICT | E_NOTICE));

include 'IchiPHP/lib/lib.php';

$app = new App();

$app->run();

//Cookie::forever('abc', 'hahdiwjdjwoaks');
echo Cookie::get('abc');