<?php

namespace PR\core;

class Router {
    public $default = "\PR\action\Home";
    public $router = [];

    /**
     * @var
     */
    public $path;

    public function __construct(array $routerMap) {
        $this->router = $routerMap;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function action($path) {
        $this->path = $path;
        $action = $this->getAction();
        return new $action();
    }

    /**
     * @return mixed
     */
    private function getAction() {
        if (isset($this->router[$this->path])) {
            return new $this->router[$this->path]();
        }
        return new $this->default();
    }
}