<?php
/**
 * Created by PhpStorm.
 * Date: 2022/8/7
 * Time: 00:17
 */

namespace PR\core;


use App;

class Tpl {
    private function __construct() { }

    /**
     * 加载tpl资源
     *
     * @param       $path
     * @param array $val
     * @param bool  $showResult
     *
     * @return mixed
     */
    public static function inc($path, $val = [], $showResult = true) {
        $file = ROOT_PATH.'/src/tpl/'.$path;
        // 将kv变成变量，让模板使用
        extract($val);
        if ($showResult) {
            return include($file);
        } else {
            ob_start();
            include($file);
            $content = ob_get_contents();
            ob_clean();
            return $content;
        }
    }

    /**
     * @param $path
     * @param $params
     *
     * @return string
     */
    public static function u($path, $params = []) {
        $pathName = App::app()->conf->getRouterActionName();
        return "/?{$pathName}={$path}&".http_build_query($params);
    }
}