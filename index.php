<?php

// 开发环境需要注册DEV
// export DEV=1

// 项目绝对跟目录
define("ROOT_PATH", dirname(__FILE__));
// 上传文件存储目录
define("DATA_PATH", ROOT_PATH."/data");

// 加载composer
require_once(ROOT_PATH."/vendor/autoload.php");

// 加载app
require_once(ROOT_PATH."/bootstrap.php");

$app = App::app();
$app->router();