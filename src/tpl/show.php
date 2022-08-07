<?php
use \PR\core\Tpl;
?>

<html>
<?
Tpl::inc('inc/vuejs.php');
Tpl::inc('inc/elementui.php');
?>
<title>项目汇报</title>
<body>
<div id="app">
    <el-row>
        <el-col :span="24">
            <template>
            <el-table
                :data="perExcel"
                border
                style="width: 100%">
                <el-table-column
                    v-for="(value, key) in perHR"
                    :key="perHR+key"
                    :prop="''+key"
                    :label="value"
                >
            </el-table>
            </template>
        </el-col>
    <el-col :span="12">
    </el-col>
    </el-row>
</div>
</body>
<script>
var app = new Vue({
    el: '#app',
    data: {
        perHR: <?=json_encode($perHR)?>,
        perExcel: <?=json_encode($perExcel)?>,
        proHR: <?=json_encode($proHR)?>,
        proExcel: <?=json_encode($proExcel)?>,
    }
})
</script>
</html>
