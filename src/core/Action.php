<?php
/**
 * Created by PhpStorm.
 * Date: 2022/8/6
 * Time: 22:47
 */

namespace PR\core;


/**
 * Class Action
 * @package PR\core
 */
abstract class Action {
    abstract function execute($app);

    public function redirect($path, $params = []) {
        $url = Tpl::u($path, $params);
        header("Location:".$url);
    }

    /**
     * 错误信息展现
     *
     * @param string $errorInfo
     * @param string $redirect
     *
     * @param int    $wait
     *
     * @return void
     */
    public function errorPage($errorInfo, $redirect = "", $wait = 3000) {
        Tpl::inc('err.php', [
            'errorInfo' => $errorInfo,
//            'redirect' => $redirect,
            'wait' => $wait,
        ]);
    }
}