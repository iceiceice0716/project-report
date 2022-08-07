<?php
use \PR\core\Tpl;
?>

<html>
<?
Tpl::inc('inc/vuejs.php');
Tpl::inc('inc/elementui.php');
Tpl::inc('inc/echart.php');

?>
<title>项目汇报</title>
<body>
<div id="app">
    <el-row>
        <el-col :span="24">
            <template>

                <el-tabs stretch="true" v-model="activeName" @tab-click="handleClick">
                    <el-tab-pane label="项目视图" name="project">
                        <div v-for="(value,key,index) in proExcel" :id="'project-chart'+key" :key="'proChart'+key"
                             class="barChart">
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="任务视图" name="task">
                        <div id="task-chart" class="barChart high"></div>

                    </el-tab-pane>
                    <el-tab-pane label="人员视图" name="people">
                        <div id="people-chart" class="barChart high2"></div>
                    </el-tab-pane>
                    <el-tab-pane label="原始数据" name="data">
                        <el-table
                                :data="perExcel"
                                border
                                style="width: 100%">
                            <el-table-column
                                    v-for="(value, key) in perHR"
                                    :key="'perHR'+key"
                                    :prop="''+key"
                                    :label="value"
                            >
                        </el-table>
                    </el-tab-pane>
                </el-tabs>

            </template>
        </el-col>
        <el-col :span="12">
        </el-col>
    </el-row>
</div>
</body>
<style>
    .barChart {
        width: 100%;
        height: 400px;
        margin-bottom: 100px;
    }

    .barChart.high {
        height: 800px;
    }

    .barChart.high2 {
        height: 1000px;
    }

</style>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            activeName: 'project',
            perHR: <?=json_encode($perHR)?>,
            perExcel: <?=json_encode($perExcel)?>,
            proHR: <?=json_encode($proHR)?>,
            proExcel: <?=json_encode($proExcel)?>,

        },
        mounted: function () {
            this.loadProjectChart();
        },
        methods: {
            handleClick(tab, event) {
                console.log(tab.name);
                let tabName = tab.name;
                switch (tabName) {
                    case 'project':
                        this.loadProjectChart();
                        break;
                    case  'task':
                        this.$nextTick(() => {
                            this.loadTaskChart();
                        });
                        break;
                    case 'people':
                        this.$nextTick(() => {
                            this.loadPeopleChart();
                        });
                        break;
                }


            },
            loadProjectChart() {
                // 处理数据
                let treeMapData = this.proExcel;
                let showMap = <?=json_encode($proShow, JSON_UNESCAPED_UNICODE)?>;

                for (let index in treeMapData) {
                    let obj = treeMapData[index]
                    let proName = obj.name;
                    let value = obj.value;
                    let data = obj.children;

                    let seriesData = [];
                    for (let proDetail of data) {
                        seriesData.push({
                            name: proDetail.name,
                            type: 'bar',
                            stack: 'total',
                            label: {
                                show: true,
                                formatter: showMap[obj.name].rowMap[proDetail.name][3] + '%',
                            },
                            emphasis: {
                                focus: 'series'
                            },
                            data: [proDetail.value]

                        })
                    }

                    let option = {
                        title: {
                            text: proName + '-进度' + value + "%",
                            left: "center"
                        },
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                // Use axis to trigger tooltip
                                type: 'shadow' // 'shadow' as default; can also be 'line' or 'shadow'
                            },
                            formatter: function (params) {
                                return showMap[obj.name].tips;
                            }
                        },
                        legend: {
                            top: '30px'
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            containLabel: true
                        },
                        xAxis: {
                            type: 'value',
                            min: 0,
                            max: 100
                        },
                        yAxis: {
                            type: 'category',
                            data: ['本周']
                        },
                        series: seriesData
                    };
                    let myChart = echarts.init(document.getElementById('project-chart' + index));
                    myChart.setOption(option);
                }


            },
            loadTaskChart() {

                let option = {
                    title: {
                        text: '项目耗时对比图'
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },

                    legend: {},
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'value',
                    },
                    yAxis: {
                        type: 'category',
                        // 项目名称
                        data: <?=json_encode($taskNames, JSON_UNESCAPED_UNICODE)?>
                    },
                    series: [
                        {
                            name: '耗费人天',
                            type: 'bar',

                            // 各项目的耗人天
                            data: <?=json_encode($taskCost)?>
                        },
                        {
                            name: '进度',
                            type: 'bar',
                            // 各项目的完成度
                            data: <?=json_encode($taskPercent)?>
                        }
                    ]
                };
                let myChart = echarts.init(document.getElementById('task-chart'));
                myChart.setOption(option);


            },

            loadPeopleChart() {

                let option = {
                    title: {
                        text: '项目人员关系图'
                    },
                    tooltip: {
                        formatter: '{b}<br />任务进度{c}%'
                    },
                    animationDurationUpdate: 1500,
                    animationEasingUpdate: 'quinticInOut',
                    series: [
                        {
                            legendHoverLink: true,
                            type: 'graph',
                            layout: 'circular',
                            force: {
                                // initLayout: 'circular'
                                //gravity: 0.5,
                                repulsion: [500, 800],
                                edgeLength: 500
                            },
                            symbolSize: 50,
                            roam: true,
                            label: {
                                show: true,
                                color: '#fff',
                                border: 'inherit'
                            },
                            edgeSymbol: ['circle', 'arrow'],
                            edgeSymbolSize: [8, 10],
                            edgeLabel: {
                                fontSize: 20
                            },
                            data: <?=json_encode($nodes, JSON_UNESCAPED_UNICODE)?>,
                            // links: [],
                            links: <?=json_encode($link, JSON_UNESCAPED_UNICODE)?>,
                            lineStyle: {
                                opacity: 0.9,
                                width: 2,
                                curveness: 0.3,
                                color: "source"
                            },
                            emphasis: {
                                focus: 'adjacency',
                                lineStyle: {
                                    width: 10
                                }
                            },
                            categories: [
                                {
                                    "name": "项目"
                                },
                                {
                                    "name": "人员"
                                }

                            ]
                        }
                    ]
                };
                let myChart = echarts.init(document.getElementById('people-chart'));
                myChart.setOption(option);


            }
        }
    })
</script>
</html>
