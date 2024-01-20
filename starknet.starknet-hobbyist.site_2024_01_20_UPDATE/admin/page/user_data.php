<?php
include '../php/api.php';
class _web extends _api
{
    public function _data()
    {
        $q = "`id`,`username`,`sex`,`picture`,`state`,`found_date`,`ip`,`comment`,`blacklist`,`admin`,
        (SELECT `name` FROM `roles_list` WHERE roles_list.id = user_data.roles_id) as roles_name,
        (SELECT COUNT(`id`) FROM  `juris_list` WHERE juris_list.roles_id = user_data.roles_id) as `count`";
        $d = $this->table('user_data', ['username:=', 'found_date', 'ip', 'state:=', 'admin:=', 'blacklist:='], $q, 'ORDER BY `id` DESC', true);
        $this->res($d);
    }

    public function _del()
    {
        $this->rdel('user_data');
    }

    public function _SetBlacklist()
    {
        $this->form([
            'id' => ['required', 'id'],
            'blacklist' => ['required', 'boolean']
        ]);
        $id = intval($_REQUEST['id']);
        $b = $_REQUEST['blacklist'] == 'true' ? '1' : '';
        if ($id === 1) $this->res('禁止修改超级管理员账号状态', 5);
        $q = "UPDATE `user_data` SET `blacklist` = '{$b}' WHERE `id` = {$id};";
        $this->run($q, false);
    }

    public function _SetAdmin()
    {
        $this->form([
            'id' => ['required', 'id'],
            'admin' => ['required', 'boolean']
        ]);
        $id = intval($_REQUEST['id']);
        $b = $_REQUEST['admin'] == 'true' ? '1' : '';
        if ($id === 1) $this->res('禁止修改超级管理员账号状态', 5);
        $q = "UPDATE `user_data` SET `admin` = '{$b}' WHERE `id` = {$id};";
        $this->run($q, false);
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>网站用户</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .roles_name {
            border-radius: 4px;
        }

        .state-0::before {
            content: '';
            width: 10px;
            height: 10px;
            background-color: #adadad;
            display: inline-block;
            border-radius: 10px;
            margin-right: 5px;
        }

        .state-0::after {
            content: '离线';
        }

        .state-1::before {
            content: '';
            width: 10px;
            height: 10px;
            background-color: #09F175;
            display: inline-block;
            border-radius: 10px;
            margin-right: 5px;
        }

        .state-1::after {
            content: '在线';
        }

        .state-2 {
            position: relative;
        }

        .state-2::before {
            content: '';
            width: 10px;
            height: 10px;
            background-color: #FD563C;
            display: inline-block;
            border-radius: 10px;
            margin-right: 5px;
        }

        .state-2::after {
            content: '忙碌';
            width: 6px;
            height: 1px;
            display: inline-block;
            background-color: #ffffff;
            position: absolute;
            top: 8px;
            left: 2px;
            line-height: 0;
            text-indent: 12px;
        }

        .state-3 {
            position: relative;
        }

        .state-3::before {
            content: '请勿打扰';
            width: 10px;
            height: 10px;
            display: inline-block;
            background-color: #ffffff;
            border-radius: 10px;
            border: 2px solid #FD563C;
            box-sizing: border-box;
            line-height: 7px;
            text-indent: 12px;
        }

        .state-3::after {
            content: '';
            width: 8px;
            height: 1px;
            background-color: #FD563C;
            display: inline-block;
            margin: auto;
            transform: rotate(-45deg);
            position: absolute;
            left: 2px;
            top: 7px;
        }

        .info-picture {
            width: 40px;
            height: 40px;
            overflow: hidden;
            display: inline-block;
            vertical-align: top;
            margin-right: 5px;
            position: relative;
        }

        .info-body {
            display: inline-block;
            vertical-align: top;
        }

        .info-picture>img {
            width: 100%;
            height: 100%;
            border-radius: 100%;
        }

        .info-username {
            font-size: 16px;
            margin-top: 5px;
            color: #333333;
        }

        .info-explain {
            color: #9eb6c3;
            font-size: 12px;
            margin-top: 3px;
        }

        .info-sex-0::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 12px;
            height: 12px;
            border-radius: 3px;
            background-color: #ffffff;
            background-size: 100% 100%;
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABdklEQVQ4T6WTv0tVYRjHP9+jJg2GtMSVe44ODi2CU0tEk39AgY1Cq917QhAXF4emRk+51VAJurW3lLgEQdAagZ4bKdUgKOKPc+83DhjoOed6g97t5f08n/d5n/d5xH8uVcXXG54IAu7bjEmMdsyL74lWq9iyoOnBSOwCw4WA1+myZoqSkiBq+DYBm6XbzM800Y2egrDpOxIbRdDmWyvReE9Bvem7gXhfEsDv1nVqLCk7f1Z6QrcM8qDTNmM7z7V9qYBp90U1PgGThSy2UnOTRMeXC4Ao9gNg/TzoDvdaz/S2Zw1yoPbIowMBXxED+d7m4KhN7deKDv5JEMZ+Ili8AJvlNNHjroL6nK8GGdOGeYmJHOyYhQA+I96dZfLB4s1xxtrfbDQy67C/j3nEQ2CocMOSIRW8vFAPc4h4ddLhqcLY24Koqs8NDcyXqsY64/cUxc6/5UrlUJqpYJ/N9jX2Bf2VwxTFdreJzjKiHytqhU1/lLhVxf0Baqp9QvXf8mgAAAAASUVORK5CYII=);
            border: 1px solid #1e6eff;
        }

        .info-sex-1::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 12px;
            height: 12px;
            border-radius: 3px;
            background-color: #ffffff;
            background-size: 100% 100%;
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABwklEQVQ4T5WSPWhTcRTFfyep+AG1aV4EJycd6iLoJKIOhQyCFrHvoYOOFqmgKIiCgyI4qqPoIopC8lqEoiCKuGkFQVzETxwdTF4qLqU0OdI00Xw8wd7pzz3n/v6XwxUp9Sv0hgUxaXNOYi3mSTbDyVxJX3vt6m1UQ59C3OjtG+bVYDSY0stOrQuQhB5qwPfmr2llvuVhi2LV23I3IPJ+w0xTNA8RB/s4phjEepYKqIz7gjJcNXwKtjKSvOe6TUGiCBRaQ5eDsi6lAqqRS4bNA3UmctN60zYloTcZzgAHEK+Dso6kbxD5S1YcGi7pXVoEtXGPNcTZINaePsBSgBZzwOxAnWND0/rcCUki77K5b8gXYq3vA9RCH22Iu60AbwWxJtomYyURb4FtzZ7YGZQ0u/xsVTXyA8xhhDCLGVEcLuvFklyNfAW42IIniPNBWbe7AJ3rViJPylwzvEIMCrbTIAqmFP/zEn3cq5Iao4jThr2CNV1ms2B4KjGTX8c93dH8nw3mQu9YhEcSG1MvsKdp83E17B6M9aOZQTXyY2Df/wz/DZabhbJOLANC1xC5FQHMh0KskSagEvo5kF0JAPhZiDX2G7SdohHaFTsyAAAAAElFTkSuQmCC);
            border: 1px solid #ed56ff;
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
                        <p>温馨提示：此处为用户列表，可以查看用户的详细信息，也可以对用户进行编辑操作。</p>
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
                                <option value="found_date">注册时间</option>
                                <option value="ip">注册IP</option>
                                <option value="state">状态</option>
                                <option value="admin">是否管理员</option>
                                <option value="blacklist">是否黑名单</option>

                            </select>
                        </div>
                        <div class="layui-input-inline" name="fieldTypeInput">
                            <div class="layui-hide">
                                <input type="text" name="username" class="layui-input" placeholder="请输入用户名" />
                            </div>
                            <div class="layui-hide">
                                <input type="text" name="found_date" class="layui-input" placeholder="选择时间" lay-type="date" />
                            </div>
                            <div class="layui-hide">
                                <input type="text" name="ip" class="layui-input" placeholder="请输入注册IP" />
                            </div>
                            <div class="layui-hide">
                                <select name="state" lay-filter="state">
                                    <option value="">全部</option>
                                    <option value="0">离线</option>
                                    <option value="1">在线</option>
                                    <option value="2">忙碌</option>
                                    <option value="3">请勿打扰</option>
                                </select>
                            </div>
                            <div class="layui-hide">
                                <select name="admin" lay-filter="admin">
                                    <option value=""></option>
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
                            </div>
                            <div class="layui-hide">
                                <select name="blacklist" lay-filter="blacklist">
                                    <option value=""></option>
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
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
                    </div>
                    <table id="user_data" lay-filter="user_data"></table>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/Sortable.min.js"></script>
<script type="text/html" id="user_dataTool">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">
            <i class="layui-icon layui-icon-add-1"></i>
            <span>新增用户</span>
        </button>
        <button class="layui-btn layui-btn-sm layui-btn-plug-danger" lay-event="Del">
            <i class="layui-icon layui-icon-delete"></i>
            <span>删除用户</span>
        </button>
    </div>
</script>
<script type='text/html' id='blacklistTool'>
    <input type='checkbox' value='{{d.id}}' lay-skin='switch' lay-filter='blacklist' {{ d.blacklist == '1' ? 'checked' : '' }} {{ d.id == '1' ? 'disabled' :''}} />
</script>
<script type='text/html' id='adminTool'>
    <input type='checkbox' value='{{d.id}}' lay-skin='switch' lay-filter='admin' {{ d.admin == '1' ? 'checked' : '' }} {{ d.id == '1' ? 'disabled' :''}} />
</script>
<script type='text/html' id='caozuoDelTool'>
    <a class='layui-table-link' lay-event='caozuo_edit' title='修改'>
        <i class="layui-icon layui-icon-edit"></i>
        <span>修改</span>
    </a>
    <span class='layui-table-divide'></span>
    <a class='layui-table-del' lay-event='caozuo_del'>
        <i class="layui-icon layui-icon-delete"></i>
        <span>删除</span>
    </a>
</script>
<script>
    table.render({
        elem: '#user_data',
        url: api.url('data'),
        page: true,
        title: "网站用户",
        toolbar: "#user_dataTool",
        skin: "line",
        size: 'lg',
        where: where(),
        cols: [
            [{
                    type: 'checkbox'
                }, {
                    field: 'username',
                    title: '用户名称',
                    width: 250,
                    sort: true,
                    templet: d => {
                        var html = `
                        <div class="info">
                            <div class="info-picture">
                                <img src="${d.picture != '' ? d.picture : '../images/picture.png'}" />
                                <i class="info-sex-${d.sex}"></i>
                            </div>
                            <div class="info-body">
                                <div class="info-username">${d.username}</div>
                            </div>
                        </div>`;
                        return html;

                    }
                }, {
                    field: 'state',
                    title: '状态 ',
                    width: 120,
                    sort: true,
                    templet: d => {
                        return '<span class="state-' + d.state + '"></span>';
                    }
                }, {
                    field: 'found_date',
                    title: '注册时间',
                    width: 170,
                    sort: true
                }, {
                    field: 'roles_name',
                    title: '角色 ',
                    width: 120,
                    sort: true,
                    templet: d => {
                        if (d.roles_name == null) return '-';
                        return d.roles_name != '' ? d.roles_name : '-';
                    }
                }, {
                    field: 'count',
                    title: '权限数量',
                    width: 120,
                    sort: true,
                    align: 'center',
                    templet: d => {
                        return d.count + '个';
                    }
                }, {
                    field: 'ip',
                    title: '注册IP',
                    width: 150,
                    sort: true,
                    templet: d => {
                        return d.ip != '' ? d.ip : '-';
                    }
                },
                {
                    field: 'comment',
                    title: '备注信息',
                    minWidth: 150,
                    sort: true
                },
                {
                    field: 'blacklist',
                    title: '黑名单',
                    templet: '#blacklistTool',
                    width: 80,
                    fixed: 'right',
                    align: 'center'
                },
                {
                    field: 'admin',
                    title: '管理员',
                    templet: '#adminTool',
                    width: 80,
                    fixed: 'right',
                    align: 'center'
                },
                {
                    field: 'caozuo',
                    title: '操作',
                    templet: '#caozuoDelTool',
                    width: 200,
                    fixed: 'right',
                    align: 'center'
                }
            ]
        ],
        done: function(res) {
            api.sort(res);
        }
    });

    table.on("toolbar(user_data)", function(obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        switch (obj.event) {
            case 'add':
                layer.open({
                    type: 2,
                    title: '新增用户',
                    area: ['850px', '550px'],
                    maxmin: false,
                    content: 'user_edit.php',
                    shade: 0.3
                });
                break;
            case 'Del':
                Del(checkStatus);
                break;
        };
    });
    form.on('select(state)', (d) => {
        reload();
    });
    form.on('select(admin)', (d) => {
        reload();
    });
    form.on('select(blacklist)', (d) => {
        reload();
    });
    form.on('switch(blacklist)', function(obj) {
        $.ajax({
            url: api.url('SetBlacklist'),
            type: 'POST',
            dataType: 'json',
            data: {
                id: this.value,
                blacklist: obj.elem.checked
            },
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
                });
                if (data.code != 1) {
                    $(obj.elem).prop('checked', !obj.elem.checked);
                    form.render('checkbox');
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    });
    form.on('switch(admin)', function(obj) {
        $.ajax({
            url: api.url('SetAdmin'),
            type: 'POST',
            dataType: 'json',
            data: {
                id: this.value,
                admin: obj.elem.checked
            },
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
                });
                if (data.code != 1) {
                    $(obj.elem).prop('checked', !obj.elem.checked);
                    form.render('checkbox');
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    });
    laydate.render({
        elem: '[name=found_date]',
        type: 'date',
        change: function(value, date, endDate) {
            setTimeout(function() {
                reload('user_data');
            }, 100);
        },
        done: function(value, date, endDate) {
            setTimeout(function() {
                reload('user_data');
            }, 100);
        }
    });

    function caozuo_del(obj) {
        layer.confirm('确定删除此用户吗？', function() {
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
                    if (data.code == 1) obj.del();
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }

    table.on('tool(user_data)', function(obj) {
        var data = obj.data;
        switch (obj.event) {
            case 'caozuo_edit':
                layer.open({
                    type: 2,
                    title: '修改用户',
                    area: ['850px', '560px'],
                    maxmin: false,
                    content: 'user_edit.php?id=' + obj.data.id,
                    shade: 0.3
                });
                break;
            case 'caozuo_del':
                caozuo_del(obj);
                break;
        };
    });

    function Del(checkStatus) {
        layer.confirm('确定删除选中的用户吗？', function() {
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
                    item: checkStatus.data
                },
                success: function(data) {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                    if (data.code == 1) reload('user_data');
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }
</script>

</html>