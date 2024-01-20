<?php
include '../php/api.php';
class _web extends _api
{
    public function _data()
    {
        $f = "`id`,`name`,`comment`,`found_date`,
        (SELECT COUNT(`id`) FROM  `juris_list` WHERE juris_list.roles_id = roles_list.id) as `count`";
        $this->table('roles_list', ['name', 'comment'], $f, ' ORDER BY `indexs`,`id` ASC');
    }

    public function _del()
    {
        $this->rdel('roles_list', function ($d) {
            $id = $d['id'];
            $q = "DELETE FROM `roles_list` WHERE `roles_id` = {$id};";
            $this->run($q);
        });
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>网站角色</title>
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
                        <p>温馨提示：角色是网站的权限组，可以给角色分配权限来控制网站的访问权限。</p>
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
                                <option value="name">角色名称</option>
                                <option value="comment">备注信息</option>
                            </select>
                        </div>
                        <div class="layui-input-inline" name="fieldTypeInput">
                            <div class="showSearch nameItem layui-hide">
                                <input type="text" name="name" class="layui-input" placeholder="请输入角色名称" />
                            </div>
                            <div class="showSearch commentItem layui-hide">
                                <input type="text" name="comment" class="layui-input" placeholder="请输入备注信息" />
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
                    <table id="roles_list" lay-filter="roles_list"></table>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/Sortable.min.js"></script>
<script type="text/html" id="roles_listTool">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">
            <i class="layui-icon layui-icon-add-1"></i>
            <span>新增角色</span>
        </button>
        <button class="layui-btn layui-btn-sm layui-btn-plug-danger" lay-event="Del">
            <i class="layui-icon layui-icon-delete"></i>
            <span>删除角色</span>
        </button>
    </div>
</script>
<script type='text/html' id='caozuoDelTool'>
    <a class='layui-table-link' lay-event='caozuo_edit' title='修改'>
        <i class="layui-icon layui-icon-edit"></i>
        <span>修改</span>
    </a>
    <span class='layui-table-divide'></span>
    <a class='layui-table-link' lay-event='caozuo_juris' title='权限'>
        <i class="layui-icon layui-icon-flag"></i>
        <span>权限</span>
    </a>
    <span class='layui-table-divide'></span>
    <a class='layui-table-del' lay-event='caozuo_del'>
        <i class="layui-icon layui-icon-delete"></i>
        <span>删除</span>
    </a>
</script>
<script>
    table.render({
        elem: "#roles_list",
        url: api.url('data'),
        page: true,
        title: "网站角色",
        toolbar: "#roles_listTool",
        skin: "line",
        where: where(),
        cols: [
            [{
                    type: 'checkbox',
                }, {
                    field: 'sort',
                    title: '排序',
                    width: 60,
                    align: 'center',
                    templet: d => {
                        return '<div class="lay-sort" data-id="' + d.id + '"></div>';
                    }
                }, {
                    field: 'name',
                    title: '角色名称',
                    width: 200,
                    sort: true
                }, {
                    field: 'count',
                    title: '权限数量',
                    width: 200,
                    sort: true,
                    align: 'center',
                    templet: d => {
                        return d.count + '个';
                    }
                }, {
                    field: 'comment',
                    title: '备注信息',
                    width: 200,
                    sort: true
                },
                {
                    field: 'found_date',
                    title: '创建时间',
                    minWidth: 170,
                    sort: true
                },
                {
                    field: 'caozuo',
                    title: '操作',
                    templet: '#caozuoDelTool',
                    width: 250,
                    fixed: 'right',
                    align: 'center'
                }
            ]
        ],
        done: function(res) {
            api.sort(res);
        }
    });

    table.on("toolbar(roles_list)", function(obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        switch (obj.event) {
            case 'add':
                add();
                break;
            case 'Del':
                Del(checkStatus);
                break;
        };
    });


    function caozuo_edit(obj) {
        layer.open({
            type: 2,
            title: '修改角色',
            area: ['550px', '350px'],
            maxmin: false,
            content: 'roles_edit.php?id=' + obj.data.id,
            shade: 0.3
        });
    }

    function caozuo_juris(obj) {
        layer.open({
            type: 2,
            title: '修改权限',
            area: ["530px", "509px"],
            maxmin: false,
            content: "set_juris.php?id=" + obj.data.id,
            shade: 0.3
        });
    }

    function caozuo_del(obj) {
        layer.confirm('确定删除此角色吗？', function() {
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
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }

    table.on('tool(roles_list)', function(obj) {
        var data = obj.data;
        switch (obj.event) {
            case 'caozuo_edit':
                caozuo_edit(obj);
                break;
            case 'caozuo_del':
                caozuo_del(obj);
                break;
            case 'caozuo_juris':
                caozuo_juris(obj);
                break;
        };
    });

    function add() {
        layer.open({
            type: 2,
            title: '新增角色',
            area: ['550px', '350px'],
            maxmin: false,
            content: 'roles_edit.php',
            shade: 0.3
        });
    }

    function Del(checkStatus) {
        layer.confirm('确定删除选中的角色吗？', function() {
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
                    if (data.code == '1') {
                        reload('roles_list');
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