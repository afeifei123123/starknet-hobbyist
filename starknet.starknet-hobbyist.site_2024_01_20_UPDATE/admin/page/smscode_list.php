<?php
include '../php/api.php';
class _web extends _api
{
    public function _data()
    {
        $this->table('smscode_list', ['tel'], "*", 'ORDER BY `id` DESC');
    }

    public function _del()
    {
        $a = $this->is('item', []);
        foreach ($a as $k) {
            $sql = "DELETE FROM `smscode_list` WHERE `id` = {$k['id']};";
            $this->run($sql);
        }
        $c = count($a);
        $this->res($c > 0 ? '删除成功' : '没有需要删除的数据', $c > 0 ? 1 : 3);
    }

    public function _clear()
    {
        $sql = "truncate table `smscode_list`";
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
    <title>短信列表</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .state_1 {
            color: #07C160;

        }

        .state_0 {
            color: #c0c4cc;
        }

        .state_0::before {
            content: "";
            display: inline-block;
            vertical-align: middle;
            width: 15px;
            height: 15px;
            margin-right: 3px;
            margin-top: -3px;
        }

        .state_0::before,
        .state_1::before {
            content: "";
            display: inline-block;
            vertical-align: middle;
            width: 15px;
            height: 15px;
            margin-right: 3px;
            margin-top: -3px;
            background-size: 100% 100%;
            border-radius: 100%;
        }

        .state_0::before {
            background-image: url(../images/state_0.png);
        }

        .state_1::before {
            background-image: url(../images/state_1.png);
        }

        .type_0 {
            color: #5897fc;
        }

        .type_0::before {
            content: '';
            display: inline-block;
            vertical-align: middle;
            width: 15px;
            height: 15px;
            margin-right: 5px;
            margin-top: -5px;
            background-repeat: no-repeat;
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABUUlEQVQ4T8WTMUhCcRCHf/eXR6WhITS+GoIchKLBliJwaaumGoKoQaOlokHUJaKmCCqQSFAhmlqamqKWCKKe0lKQPqtFBIfADKHB3rswseEJQip023F3Hx93HKHJoCbn0TqAy8urDpkXbRaUjFbvRbSnMhRORGnfWPs1cHn46DhYGurswICxKV/EvWdHiisRWvofwEcRFwvbUigeo7M/GzAQlmfHfflsbpII/SboJ1ZVTVZB9XfAnO6anhj9essoAHp/hphf20gfM6tqtpzWBRDzltk9+AlGgIis0LHGonwlXbarqUANYMat9fV0s1bVyxXE6VzQaQG4AMGPFQNxDuDAnk76agBEmK80IaFEyTXsZf/llSOmsbhjRoQIGwAkwTRie366NQJWQNgjQDAjx8AyEdaJ8bB7PbXp1FU/iCSTxofWl+RNzRIb/YnW/UKjBt/fIZgRHQCPFgAAAABJRU5ErkJggg==);
        }

        .type_1 {
            color: #ff7f33;
        }

        .type_1::before {
            content: '';
            display: inline-block;
            vertical-align: middle;
            width: 15px;
            height: 15px;
            margin-right: 5px;
            margin-top: -5px;
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABy0lEQVQ4T52TS2gTURSGvzPXkogLA4IotfhgWqyKIi5KgiCCC9FNpiCI4Fpw6aMugphtm4puVKS4EcGFkGiViiiIgRqhG19gNFGK+MClhUIzzcyRDMk4aY0G7+6e+5/v/OdcjrDkaJYVzNtngOMIW0Es0M+o3GS2kpU7eNEUWQYYsS+AZIFvqFaCd5G1wCDKbclVjv0L8B6ln7rVJ5c+fG2I9QiGzXYZxEa8hIx++tmC/MFB/xyq85Krro9W0hG7AJLGWxyUi7PlzoBzW/biWZ6MV0v/BVg6k9a9awedAJ3i4QwWSvmHghxssw1VARfY1hZXHsVTTqDtCFD1i7HJzEp8r7e+27nhb0qeD9vpBmC+vL5uZm6daCYVXWdsD7Aq+NZuAFJ3H/dMZgYQ+vyB/Zfr2w+d+quD2vP8A0QOR3s15adXzbupfTVnLCFIb+RtKpZ0Am1kBoUrAifbAJXihHl7P+U6uXXAmtABXIsnnUAbAmrT+R0ILxExLaH5OD1hXt0bcodHN4KsbsYVlV2xVPpNG6BxWXhROC3KeFjJX3xmzf34rokNR3/3r2fjqeFQs2wX3NLdocb6KHoAZGfTpQIzFpLpSaafRNv8BZYhvBE6S9M9AAAAAElFTkSuQmCC);
        }
    </style>
</head>

<body class="layui-form">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-msg">
                        <i class="layui-icon layui-icon-tips"></i>
                        <p>温馨提示：短信配置信息，请先在【系统设置】-【网站设置】-【接口设置】-【SMS短信】中配置短信接口信息。</p>
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
                            <input type="text" name="tel" class="layui-input" placeholder="请输入手机号码" />
                        </div>
                        <button class="layui-btn layui-btn-sm layui-btn-normal search">
                            <i class="layui-icon layui-icon-search"></i>
                            <span>查询</span>
                        </button>
                        <button class="layui-btn layui-btn-sm layui-btn-plug-danger clear">
                            <i class="layui-icon layui-icon-delete"></i>
                            <span>清空短信</span>
                        </button>
                    </div>
                    <table id="smscode_list" lay-filter="smscode_list"></table>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script type='text/html' id='stateTool'>
    <span class="state_{{ d.state }}">{{ d.state == "1" ? "已验证" : "未验证" }}</span>
</script>
<script type='text/html' id='caozuoDelTool'>
    <a class="layui-table-link" lay-event="caozuo_copy">
        <i class="layui-icon layui-icon-form"></i>
        <span>复制验证码</span>
    </a>
    <span class="layui-table-divide"></span>
    <a class='layui-table-del' lay-event='caozuo_del'>
        <i class="layui-icon layui-icon-delete"></i>
        <span>删除</span>
    </a>
</script>
<script>
    table.render({
        elem: '#smscode_list',
        url: api.url('data'),
        page: true,
        skin: 'line',
        title: "短信列表",
        where: where(),
        cols: [
            [{
                field: 'type',
                title: '业务类型',
                width: 120,
                align: 'center',
                templet: d => {
                    return `<span class="type_${d.type}">${d.type == "1" ? "密码找回" : "帐号注册" }</span>`;
                },
                sort: true
            }, {
                field: 'tel',
                title: '手机号码',
                width: 180,
                align: 'center',
                sort: true
            }, {
                field: 'smscode',
                title: '验证码',
                width: 150,
                align: 'center',
                sort: true
            }, {
                field: 'found_date',
                title: '发送时间',
                width: 200,
                sort: true
            }, {
                field: 'veri_date',
                title: '验证时间',
                width: 200,
                sort: true,
                templet: d => {
                    return d.veri_date == '0000-00-00 00:00:00' ? '-' : d.veri_date;
                }
            }, {
                field: 'state',
                title: '验证状态',
                width: 120,
                align: 'center',
                templet: '#stateTool',
                sort: true
            }, {
                field: 'ip',
                title: '操作IP',
                minWidth: 150,
                sort: true
            }, {
                field: 'caozuo',
                title: '操作',
                templet: '#caozuoDelTool',
                width: 250,
                fixed: 'right',
                align: 'center'
            }]
        ]
    });

    table.on('tool(smscode_list)', function(obj) {
        var data = obj.data;
        switch (obj.event) {
            case 'caozuo_del':
                caozuo_del(obj);
                break;
            case 'caozuo_copy':
                api.copy(obj.data.smscode, '已复制验证码');
                break;
        };
    });

    $('.clear').click(function() {
        layer.confirm('确定清空所有短信信息吗？', function(index) {
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
                        reload();
                    });
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    });

    function caozuo_del(obj) {
        layer.confirm('确定删除此验证码吗？', function() {
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
                    if (data.code == 1) {
                        api.tableDel();
                        obj.del();
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }
</script>

</html>