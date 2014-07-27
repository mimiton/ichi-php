<?php
// 关闭不需要的 error report
error_reporting(E_ALL &~ (E_STRICT | E_NOTICE));
// 载入核心库
require 'IchiPHP/lib/lib.php';
// 配置信息
include 'config.php';

// 启动
App::run();

//debugging
//Cookie::forever('abc', 'hahdiwjdjwoaks');
//echo '<h3>cookie:abc='.Cookie::get('abc').'</h3>';