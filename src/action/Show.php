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

class Show extends Action
{
    private $tplValue = [];
    private $tmpData = [];

    function execute($app)
    {
        // 上传的文件
        $d = $_GET['d'];
        $dataPath = DATA_PATH . '/' . $d;
        if (empty($d) || !file_exists($dataPath)) {
            $this->errorPage('未找到上传记录', Tpl::u(constant::ACTION_UPLOAD));
            return;
        }
        $perCsv = $dataPath . DIRECTORY_SEPARATOR . 'personal.csv';
        $proCsv = $dataPath . DIRECTORY_SEPARATOR . 'project.csv';
        if (!file_exists($perCsv) || !file_exists($proCsv)
        ) {
            $this->errorPage('丢失csv文件', Tpl::u(constant::ACTION_UPLOAD));
            return;
        }

        // 按照行读取文件
        $personal = file_get_contents($perCsv);
        $project = file_get_contents($proCsv);

        // 解析personal
        $this->parsePersonal($personal);

        // 解析project
        $this->parseProject($project);

        // parser all
        $this->parseAll();

        // 加载模板
        Tpl::inc('show.php', $this->tplValue);
    }

    /**
     * 设置模板变量
     * @param $key
     * @param $value
     *
     * @return void
     */
    private function assign($key, $value)
    {
        $this->tplValue[$key] = $value;
    }

    private function parsePersonal($personal)
    {
        // 填写者名字,填写者邮箱,填写者部门,填写者 ID,收集来源,提交时间,
        // 所属项目-工作内容1,详细工作内容-工作内容1,耗费人天(天)-工作内容1,完成度-工作内容1,
        // 所属项目-工作内容2,详细工作内容-工作内容2,耗费人天(天)-工作内容2,完成度-工作内容2,
        // 所属项目-工作内容3,详细工作内容-工作内容3,耗费人天(天)-工作内容3,完成度-工作内容3,
        // 所属项目-工作内容4,详细工作内容-工作内容4,耗费人天(天)-工作内容4,完成度-工作内容4,
        // 所属项目-工作内容5,详细工作内容-工作内容5,耗费人天(天)-工作内容5,完成度-工作内容5


        // 拆变行规则
        $rule = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            [0, 1, 2, 3, 4, 5, 11, 12, 13, 14,15],
            [0, 1, 2, 3, 4, 5, 16, 17, 18, 19,20],
            [0, 1, 2, 3, 4, 5, 21, 22, 23, 24,25],
            [0, 1, 2, 3, 4, 5, 26, 27, 28, 29,30],
        ];
        // 必须有的字段，过滤无用行
        $mastHas = [
            0, 0, 0, 0, 0, 0, 1, 0, 1, 1,1
        ];
        // 数据预处理
        $personal = $this->csvArr($personal);
        $hr = array_shift($personal);// 标题行
        $hr = array_slice($hr, 0, 11); // 只要前10个
        $hr = array_map(function ($v) {
            return rtrim($v, '12345');
        }, $hr); // 去除标题中的123
        $data = [];
        foreach ($personal as $cols) {
            foreach ($rule as $keys) {
                $c = [];
                foreach ($keys as $index => $key) {
                    $c[] = $cols[$key];
                    // 必须存在并且为空，直接这行就不要了
                    if ($mastHas[$index] && empty($cols[$key])) {
                        continue 2;
                    }
                }
                $data[] = $c;
            }
        }

        // 缓存结果
        $this->tmpData['personal'] = $data;

        $this->assign('perHR', $hr);
        $this->assign('perExcel', $data);
    }

    private function parseProject($project)
    {
        $project = $this->csvArr($project);
        $hr = array_shift($project);// 标题行

        // 缓存结果
        $this->tmpData['project'] = $project;

        $treeData = [];
        $proMap = [];
        foreach ($project as $row) {
            $proMap[$row[0]][] = $row;
        }
        $proShow = [];
        foreach ($proMap as $pro => $rows) {
            $treeData[] = [
                'name' => $pro,
                'value' => array_sum(
                    array_map(function ($r) {
                        return $r[3] * $r[4] / 100;
                    }, $rows)
                ),
                'children' => array_map(function ($r) {
                    return [
                        'name' => $r[1],
                        'value' => $r[3] * $r[4] / 100,
                    ];
                }, $rows),
            ];
            $proShow[$pro] = [
                'rowMap' => array_combine(array_column($rows, 1), $rows),
                'tips' => implode('<br/>', array_map(function($r) {
                    return "{$r[1]}：<b>{$r[3]}%</b>";
                }, $rows))
            ];
        }
        $this->assign('proHR', $hr);
        $this->assign('proExcel', $treeData);
        $this->assign('proShow', $proShow);

    }

    private function csvArr($s)
    {
        $colSplit = ',';
        $lineSplit = "\n";
        // 一个字符一个的过，要解决双引号的问题
        $s = trim($s) . $lineSplit;
        $len = strlen($s);
        $table = [];
        $tr = [];
        $td = '';
        $valMod = false;
        for ($i = 0; $i < $len; $i++) {
            $c = $s[$i];
            // 双引号
            if ($c == '"') {
                $valMod = !$valMod;
                continue;
            }
            if (!$valMod) {
                // 分行
                if ($c == $lineSplit) {
                    $tr[] = trim($td);
                    $td = '';
                    $table[] = $tr;
                    $tr = [];
                    continue;
                }
                // 分列
                if ($c == $colSplit) {
                    $tr[] = trim($td);
                    $td = '';
                    continue;
                }
            }
            $td .= $c;
        }
        return $table;
    }

    private function parseAll()
    {
        $personal = $this->tmpData['personal'];
        $project = $this->tmpData['project'];

        // 任务名字列表
        $taskNames = array_map(function ($v) {
            return $v[0] . '-' . $v[1];
        }, $project);
        $this->assign('taskNames', $taskNames);

        // 任务人天
        $taskCost = array_map(function ($tn) use ($personal) {
            return doubleval(array_reduce($personal, function ($r, $v) use ($tn) {
                if ($v[6] == $tn) {
                    $r = doubleval($r) + doubleval($v[8]);
                }
                return $r;
            }));
        }, $taskNames);

        $this->assign('taskCost', $taskCost);

        // 任务完成度
        $taskPercent = array_map('intval', array_column($project, 3));
        $this->assign('taskPercent', $taskPercent);


        // 关系图 边的个数
        $nodeCnt = [];
        foreach ($personal as $v) {
            $nodeCnt[$v[0]]++;
            $nodeCnt[$v[6]]++;
        }

        // 关系图 节点
        $nodes = array_merge(array_map(function ($v) use ($nodeCnt) {
            $name = $v[0] . '-' . $v[1];
            return [
                'name' => $name,
                'symbolSize' => intval($nodeCnt[$name]) * 15 + 40,
                'category' => 0,
            ];
        }, $project), array_map(function ($n) use ($nodeCnt) {
            return [
                'name' => $n,
                'symbolSize' => intval($nodeCnt[$n]) * 15 + 40,
                'category' => 1,
            ];
        }, array_unique(array_column($personal, 0))));
        $this->assign('nodes', $nodes);

        // 关系图 边
        $link = array_map(function ($v) {
            return [
                'source' => $v[6],
                'target' => $v[0],
                'value' => $v[9],
                'label' => [
                    'show' => true,
                    'formatter' => $v[9],
                    'fontSize' => 16,

                ],
            ];
        }, $personal);
        $this->assign('link', $link);
        /*var_dump($nodes, $link);
        die;*/

    }
}
