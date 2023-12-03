<?php
include '../php/api.php';
class _web extends _api
{
    public function _data()
    {
        $f = "`id`,`path`,`method`,`ip`,`found_date`,
        (SELECT `username` FROM `user_data` WHERE user_data.id = juris_log.user_id limit 1) AS `username`";
        $this->table('juris_log', ['path', 'ip', 'found_date'], $f, 'ORDER BY `id` DESC');
    }

    public function _del()
    {
        $this->rdel('juris_log');
    }

    public function _clear()
    {
        $sql = "truncate table `juris_log`";
        $this->run($sql, false);
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>权限访问日志</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-msg">
                        <i class="layui-icon layui-icon-tips"></i>
                        <p>温馨提示：权限访问日志记录了所有无权限访问者访问的信息，包括访问路径、访问者IP、访问时间、访问者账号等信息。</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-form-item">
                        <label class="layui-form-label">查询条件</label>
                        <div class="layui-input-inline">
                            <select name="fieldTypeSelect" lay-verify="required" lay-filter="fieldTypeSelect" lay-search>
                                <option value=""></option>
                                <option value="path">访问路径</option>
                                <option value="ip">访问者IP</option>
                                <option value="found_date">访问时间</option>
                            </select>
                        </div>
                        <div class="layui-input-inline" name="fieldTypeInput">
                            <div class="showSearch pathItem layui-hide">
                                <input type="text" name="path" class="layui-input" placeholder="请输入访问路径" />
                            </div>
                            <div class="showSearch ipItem layui-hide">
                                <input type="text" name="ip" class="layui-input" placeholder="请输入访问者IP" />
                            </div>
                            <div class="showSearch found_dateItem layui-hide">
                                <input type="text" name="found_date" class="layui-input" placeholder="选择时间" lay-type="date" />
                            </div>
                        </div>
                        <button class="layui-btn layui-btn-sm layui-btn-normal search">
                            <i class="layui-icon layui-icon-search"></i>
                            <span>查询</span>
                        </button>
                        <button class="layui-btn layui-btn-sm layui-btn-primary resetting">
                            <i class="layui-icon layui-icon-refresh"></i>
                            <span>重置条件</span>
                        </button>
                        <button class="layui-btn layui-btn-sm layui-btn-plug-danger clear">
                            <i class="layui-icon layui-icon-delete"></i>
                            <span>清空日志</span>
                        </button>
                    </div>
                    <table id="juris_log" lay-filter="juris_log"></table>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script type='text/html' id='caozuoDelTool'>
    <a class='layui-table-del' lay-event='caozuo_del'>
        <i class="layui-icon layui-icon-delete"></i>
        <span>删除</span>
    </a>
</script>
<script>
    table.render({
        elem: "#juris_log",
        url: api.url('data'),
        page: true,
        title: "权限访问日志",
        skin: "line",
        where: where(),
        cols: [
            [{
                field: 'username',
                title: '用户名称',
                width: 200,
                sort: true
            }, {
                field: 'path',
                title: '访问路径',
                width: 300,
                sort: true
            }, {
                field: 'method',
                title: '方法名称',
                width: 300,
                sort: true
            }, {
                field: 'ip',
                title: '访问者IP',
                width: 150,
                sort: true
            }, {
                field: 'found_date',
                title: '访问时间',
                minWidth: 170,
                sort: true
            }, {
                field: 'caozuo',
                title: '操作',
                templet: '#caozuoDelTool',
                width: 150,
                fixed: 'right',
                align: 'center'
            }]
        ]
    });

    laydate.render({
        elem: '[name=found_date]',
        type: 'date',
        change: function(value, date, endDate) {
            setTimeout(function() {
                reload('juris_log');
            }, 100);
        },
        done: function(value, date, endDate) {
            setTimeout(function() {
                reload('juris_log');
            }, 100);
        }
    });

    $('.clear').click(function() {
        layer.confirm('确定清空所有日志信息吗？', function(index) {
            $.ajax({
                url: api.url('clear'),
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    layer.msg("正在执行", {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    });
                },
                success: function(data) {
                    layer.msg(data.msg, {
                        icon: data.code
                    }, function() {
                        reload('juris_log');
                    });
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    });

    function caozuo_del(obj) {
        layer.confirm('确定删除此记录吗？', function() {
            var arr = [];
            arr[0] = obj.data;
            $.ajax({
                url: api.url('del'),
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    layer.msg('删除中', {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    });
                },
                data: {
                    item: arr
                },
                success: function(data) {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                    if (data.code == '1') {
                        obj.del();
                        api.tableDel();
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }

    table.on('tool(juris_log)', function(obj) {
        var data = obj.data;
        switch (obj.event) {
            case 'caozuo_del':
                caozuo_del(obj);
                break;
        };
    });
</script>

</html>