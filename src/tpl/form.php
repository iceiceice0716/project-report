<?php

use PR\constant;
use PR\core\Tpl;
?>
<html>
<title>项目汇报-上传</title>
<body>
<div id="app">
    <?php
    if (isset($showError) && $showError) {
    ?>
        <div id="errorInfo"><?=$errInfo?></div>
    <?php
    }
    ?>
    <form action="<?=Tpl::u(constant::ACTION_UPLOAD)?>" method="post" enctype="multipart/form-data">
        工作周报：<input name="personal" type="file"><br/>
        项目进度：<input name="project" type="file"><br/>
        <input type="submit" value="提交">
    </form>
</div>
</body>
</html>
