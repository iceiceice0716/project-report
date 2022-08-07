<?php
/**
 * Created by PhpStorm.
 * Date: 2022/8/6
 * Time: 23:03
 */

namespace PR\core;


use App;

class Context {
    /** @var App */
    private $app;

    public function __construct(App $app) {
        $this->app = $app;
    }

    public function getRouter() {
        return $this->app->router;
    }
}