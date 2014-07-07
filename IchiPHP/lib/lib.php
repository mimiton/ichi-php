<?php

// 定义 IchiPHP 所在目录
defined('ICHI_PHP_PATH') or define('ICHI_PHP_PATH',     'IchiPHP');

// 包含各模块
require 'core/Cookie.php';
require 'core/Request.php';
require 'core/Response.php';
require 'core/Router.php';
require 'core/App.php';

?>