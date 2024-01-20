<?php
include '../php/api.php';
class _web extends _api
{
    public function _data()
    {
        $u = $this->is('username');
        $t = $this->is('type');
        $d = $this->is('found_date');
        $f = "`found_date`,`type`,`os`,`ip`,`area`,
		(SELECT `username` FROM `user_data` WHERE user_data.id = logon_log.user_id limit 1) AS `username`";
        $w = '';
        if ($t) $w .= $w ? " AND `type` = '{$t}'" : "`type` = '{$t}'";
        if ($d) $w .= $w ? " AND `found_date` = '{$d}'" : "`found_date` = '{$d}'";
        if ($u) {
            $sql = "SELECT `id` FROM `user_data` WHERE `username` = '{$u}' limit 1;";
            $res = $this->run($sql);
            if ($res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $w .= $w ? " AND `user_id` = '{$row['id']}'" : "`user_id` = '{$row['id']}'";
            } else {
                $w .= $w ? " AND `user_id` = '0'" : "`user_id` = '0'";
            }
        }
        if ($w) $w = " WHERE {$w}";
        $w .= " ORDER BY `id` DESC";
        $this->query('logon_log', $w, $f);
    }

    public function _clear()
    {
        $sql = "truncate table `logon_log`";
        $this->run($sql, false);
    }
};
$web = new _web(2, 'id', false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>登录日志</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=20201111001" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=20201111001" />
</head>

<body class="layui-form">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-msg">
                        <i class="layui-icon layui-icon-tips"></i>
                        <p>温馨提示：登录日志记录了用户的登录信息，包括登录时间、登录类型、登录设备、登录IP、登录地区等信息。</p>
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
                                <option value="username">用户名</option>
                                <option value="type">登录类型</option>
                                <option value="found_date">登录时间</option>
                            </select>
                        </div>
                        <!-- 可选的查询框 -->
                        <div class="layui-input-inline" name="fieldTypeInput">
                            <div class="showSearch usernameItem layui-hide">
                                <input type="text" name="username" class="layui-input" placeholder="请输入用户名" />
                            </div>
                            <div class="showSearch typeItem layui-hide">
                                <select name="type" lay-filter="type" class="layui-hide">
                                    <option value="">全部</option>
                                    <option value="0">电脑登录</option>
                                    <option value="1">手机登录</option>
                                    <option value="2">APP登录</option>
                                </select>
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
                            <span>清空记录</span>
                        </button>
                    </div>
                    <table id="logon_log" lay-filter="logon_log"></table>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="/dist/layui/layui.js?v=20201111001"></script>
<script src="../js/api.js?v=20201111001"></script>
<script>
    var $ = layui.$,
        table = layui.table,
        form = layui.form,
        upload = layui.upload,
        laydate = layui.laydate;
    table.render({
        elem: "#logon_log",
        url: api.url('data'),
        page: true,
        title: "登录日志",
        skin: "line",
        where: where(),
        cols: [
            [{
                field: 'found_date',
                title: '登录时间',
                width: 200,
                sort: true
            }, {
                field: 'username',
                title: '用户名',
                sort: true,
                width: 200
            }, {
                field: 'type',
                title: '登录类型',
                width: 200,
                sort: true,
                templet: d => {
                    switch (d.type) {
                        case '0':
                            return '电脑登录';
                            break;
                        case '1':
                            return '手机登录';
                            break;
                        default:
                            return 'APP登录';
                            break;
                    }
                }
            }, {
                field: 'os',
                title: '设备名称',
                width: 200,
                sort: true
            }, {
                field: 'ip',
                title: '登录IP',
                width: 200,
                sort: true
            }, {
                field: 'area',
                title: '地区',
                minWidth: 200,
                templet: d => {
                    return d.area ? d.area : '-';
                },
                sort: true
            }]
        ]
    });
    table.on('tool(logon_log)', function(obj) {
        var data = obj.data;
        switch (obj.event) {
            case 'caozuo_edit':
                caozuo_edit(obj);
                break;
            case 'caozuo_del':
                caozuo_del(obj);
                break;
        };
    });

    form.on('select(type)', function(data) {
        reload('logon_log');
    });

    laydate.render({
        elem: '[name=found_date]',
        type: 'date',
        change: function(value, date, endDate) {
            setTimeout(function() {
                reload('logon_log');
            }, 100);
        },
        done: function(value, date, endDate) {
            setTimeout(function() {
                reload('logon_log');
            }, 100);
        }
    });
    $(document).on('click', '.clear', function() {
        layer.confirm('确定清空所有登录记录吗？', (index) => {
            clear(index);
        });
    });

    function caozuo_edit(obj) {
        var id = obj.data.id;
        layer.open({
            type: 2,
            title: '编辑数据',
            area: ['437px', '420px'],
            maxmin: false,
            content: 'logon_log_add.php?id=' + id,
            shade: 0.3
        });
    }

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
                    layer.msg(data.code, {
                        icon: data.icon
                    });
                    if (data.icon == '1') {
                        obj.del();
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }

    function clear(index) {
        $.ajax({
            url: api.url('clear'),
            type: 'POST',
            dataType: 'json',
            beforeSend: () => {
                layer.msg('正在加载', {
                    icon: 16,
                    shade: 0.05,
                    time: false
                });
            },
            success: r => {
                layer.close(index);
                layer.msg(r.msg, {
                    icon: r.code
                });
                if (r.code == 1) reload('logon_log');
            },
            error: (r) => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }
</script>

</html>