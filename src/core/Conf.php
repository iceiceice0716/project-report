<?php
/**
 * Created by PhpStorm.
 * Date: 2022/8/6
 * Time: 23:44
 */

namespace PR\core;


class Conf {
    private $conf = [];
    public function __construct(array $conf) {
        $this->conf = $conf;
    }

    /**
     * 是否是开发环境
     * @return bool
     */
    public function debug() {
        return !!getenv("DEV");
    }

    /**
     * 路由名字
     * @return string
     */
    public function getRouterActionName() {
        return $this->conf['router']['actionName'] ?: "a";
    }

    /**
     * 路由映射
     * @return array
     */
    public function getRouterConf() {
        return $this->conf['router']['actionMap'] ?: [];
    }
}