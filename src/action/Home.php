<?php
/**
 * Created by PhpStorm.
 * Date: 2022/8/6
 * Time: 22:21
 */

namespace PR\action;


use App;
use PR\constant;
use PR\core\Action;

class Home extends Action {
    function execute($app) {
        // 直接跳转到upload
        $this->redirect(constant::ACTION_UPLOAD);
    }
}