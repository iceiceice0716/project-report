<?php

// <!-- 开发环境版本，包含了有帮助的命令行警告 -->
$dev = <<<VUEJS
<script src="//cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
d
VUEJS;

// <!-- 生产环境版本，优化了尺寸和速度 -->
$online = <<<VUEJS
<script src="//cdn.jsdelivr.net/npm/vue@2"></script>

VUEJS;

if (App::app()->conf->debug()) {
    echo $dev;
} else {
    echo $online;
}
