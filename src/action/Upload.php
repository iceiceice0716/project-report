<?php
/**
 * Created by PhpStorm.
 * Date: 2022/8/6
 * Time: 22:22
 */

namespace PR\action;


use PR\constant;
use PR\core\Action;
use PR\core\Tpl;

class Upload extends Action {

    function execute($app) {
        $showInfo = [];
        if ($_FILES) {
            $errInfo = $this->post();
            if (!$errInfo) {
                return;
            }
            $showInfo = [
                'showError' => true,
                'errInfo' => $errInfo,
            ];
        }
        $this->form($showInfo);
    }

    /**
     * 表单展示
     * @param $showInfo
     * @return void
     */
    private function form($showInfo) {
        Tpl::inc('form.php', $showInfo);
    }

    /**
     * post数据处理
     * @return string
     */
    private function post() {
        if (empty($_FILES) || empty($_FILES['personal']) || empty($_FILES['project'])) {
            return '没有检测到文件';
        }
        /**
        ["name"]=> string(13) "file_origin_name.csv"
        ["type"]=> string(24) "text/csv"
        ["tmp_name"]=> string(66) "/path/to/tmp"
        ["error"]=> int(0)
        ["size"]=> int(xxxxx)
         */
        $personalInfo = $_FILES['personal'];
        $projectInfo = $_FILES['project'];
        // 检测是否是csv
        if ($personalInfo['type'] != 'text/csv' || $projectInfo['type'] != 'text/csv') {
            return '仅支持上传csv格式的文件';
        }

        // 移动文件到data
        $d = date("Ymd").DIRECTORY_SEPARATOR.uniqid(time());
        $dateDir = DATA_PATH.DIRECTORY_SEPARATOR.$d;
        if (!file_exists($dateDir) || !is_dir($dateDir)) {
            mkdir($dateDir, 0777, true);
        }
        if (!move_uploaded_file($personalInfo['tmp_name'], $dateDir.DIRECTORY_SEPARATOR.'personal.csv')
            || !move_uploaded_file($projectInfo['tmp_name'], $dateDir.DIRECTORY_SEPARATOR.'project.csv')
        ) {
            return '文件上传失败，请查看上传文件夹权限和服务器状态';
        }
        // 上传成功就跳转展现页面
        $this->redirect(constant::ACTION_SHOW, [
            'd' => $d,
        ]);
        return '';
    }
}