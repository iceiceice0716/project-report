<?php


use PR\core\Conf;
use PR\core\Context;
use PR\core\Router;

class App {
    /** @var Router */
    public $router;
    /** @var Conf */
    public $conf;

    public static function app() {
        static $app = null;
        if (is_null($app)) {
            $app = new App();
        }
        return $app;
    }

    private function __construct() {
        $this->init();
    }

    /**
     * 路由到action
     */
    public function router() {
        $actionParamName = $this->conf->getRouterActionName();
        $path = $_GET[$actionParamName];
        $action = $this->router->action($path);
        $action->execute(new Context($this));
    }

    /**
    * 初始化所有东西
    */
    private function init() {
        $this->initConf();
        $this->initRouter();
    }

    /**
     * 初始化配置
     */
    private function initConf() {
        $this->conf = new Conf(include(ROOT_PATH."/src/conf/app.php"));
    }

    /**
     * 初始化路由
     */
    private function initRouter(){
        $this->router = new Router($this->conf->getRouterConf());
    }
}
