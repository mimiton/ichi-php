<?php

// 当前工作目录
defined('ICHI_CWD') or define('ICHI_CWD', getcwd());

// 定义 IchiPHP 所在目录
defined('ICHI_PHP_PATH')         or define('ICHI_PHP_PATH',         ICHI_CWD . '/IchiPHP');

// 定义 app 目录
defined('ICHI_APP_PATH')         or define('ICHI_APP_PATH',         ICHI_PHP_PATH . '/app');

// 定义 控制器 目录
defined('ICHI_CONTROLLERS_PATH') or define('ICHI_CONTROLLERS_PATH', ICHI_APP_PATH . '/controllers');
// 定义 控制器 命名空间
defined('ICHI_CONTROLLERS_NS')   or define('ICHI_CONTROLLERS_NS',   '\app\controllers');

// 定义 模型 目录
defined('ICHI_MODELS_PATH')      or define('ICHI_MODELS_PATH',      ICHI_APP_PATH . '/models');
// 定义 模型 命名空间
defined('ICHI_MODELS_NS')        or define('ICHI_MODELS_NS',        '\app\models');




// 包含各模块
require 'core/Cookie.php';
require 'core/Request.php';
require 'core/Response.php';
require 'core/Router.php';
require 'core/App.php';

?>