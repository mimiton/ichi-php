<?php

// 默认加密密匙
defined('ICHI_SALT') or define('ICHI_SALT', 'iu87sja01kgnvkd2');


/** ** ** ** ** ** ** ** ** ** ** ** ** **  文件路径 ** ** ** ** ** ** ** ** ** ** ** ** ** **/

// 当前工作目录
defined('ICHI_CWD') or define('ICHI_CWD', getcwd());
// 定义 IchiPHP 所在目录
defined('ICHI_PHP_PATH')         or define('ICHI_PHP_PATH',         ICHI_CWD . '/IchiPHP');

// 定义 app 目录
defined('ICHI_APP_PATH')         or define('ICHI_APP_PATH',         ICHI_CWD . '/app');
// 定义 extend 目录
defined('ICHI_EXTEND_PATH')      or define('ICHI_EXTEND_PATH',       ICHI_PHP_PATH . '/extend');

// 定义 控制器 目录
defined('ICHI_CONTROLLERS_PATH') or define('ICHI_CONTROLLERS_PATH', ICHI_APP_PATH . '/controllers');
// 定义 模型 目录
defined('ICHI_MODELS_PATH')      or define('ICHI_MODELS_PATH',      ICHI_APP_PATH . '/models');
// 定义 视图 目录
defined('ICHI_VIEWS_PATH')       or define('ICHI_VIEWS_PATH',        ICHI_APP_PATH . '/views');
// 定义 驱动 目录
defined('ICHI_DRIVERS_PATH')     or define('ICHI_DRIVERS_PATH',     ICHI_EXTEND_PATH . '/drivers');

/** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/



/** ** ** ** ** ** ** ** ** ** ** ** ** **  命名空间 ** ** ** ** ** ** ** ** ** ** ** ** ** **/

// 定义 控制器 命名空间
defined('ICHI_CONTROLLERS_NS')   or define('ICHI_CONTROLLERS_NS',   '\controllers');
// 定义 模型 命名空间
defined('ICHI_MODELS_NS')        or define('ICHI_MODELS_NS',        '\models');
// 定义 扩展 命名空间
defined('ICHI_EXTEND_NS')        or define('ICHI_EXTEND_NS',        '\extend');
// 定义 驱动 命名空间
defined('ICHI_DRIVERS_NS')       or define('ICHI_DRIVERS_NS',       '\extend\drivers');

/** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/

/** ** ** ** ** ** ** ** ** ** ** ** ** ** 特殊URI常量 ** ** ** ** ** ** ** ** ** ** ** ** ** **/

// 应用首页控制器URI
defined('ICHI_URI_APP_HOMEPAGE')  or define('ICHI_URI_APP_HOMEPAGE',  '/_config/_homepage');

// 应用初始化控制器URI
defined('ICHI_URI_APP_INIT')      or define('ICHI_URI_APP_INIT',  '/_config/_init');

// 应用异常控制器URI
defined('ICHI_URI_EXCEPTION')     or define('ICHI_URI_EXCEPTION',  '/_config/_exception');

/** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/


// 包含各模块
require 'core/App.php';
require 'core/Cookie.php';
require 'core/DomainConfiger.php';
require 'core/Loader.php';
require 'core/Driver.php';
require 'core/Extend.php';
require 'core/Interfaces.php';
require 'core/Model.php';
require 'core/Request.php';
require 'core/Response.php';
require 'core/Router.php';
require 'core/Session.php';
require 'core/SQL.php';
require 'core/Util.php';
?>