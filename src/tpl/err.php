<?php
    $script = "";
    if (!isset($wait) || !$wait) {
        $wait = 3000;
    }
    if (isset($redirect) && $redirect) {
        $script .= "<script>setTimeout(function(){window.location.href='{$redirect}';},{$wait});</script>";
    }
?>

<html>
<meta content="text/html; charset=utf-8" />
<title>发生错误</title>
<body>
<div id="app">
    <h1>:(</h1>
    <h2><?=$errorInfo?></h2>
    <?=isset($redirect) ? '<h3><span id="t"></span>秒后即将跳转</h3>': ''?>
</div>
</body>
<?=$script?>
<script>
(function countDown (t){
    if (t <= 0) {
        return;
    }
    var d = document.getElementById('t');
    if (d) {
        d.innerHTML = '' + (t / 1000);
    }
    setTimeout(function(){
        countDown(t - 1000)
    }, 1000);
})(<?=$wait?>)
</script>
</html>
