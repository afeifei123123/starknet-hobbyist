<?php
include '../php/api.php';
class _web extends _api
{
    public function _data()
    {
        $this->table('juris_data', ['name', 'path', 'comment', 'found_date'], "*", 'ORDER BY `found_date` DESC');
    }

    public function _del()
    {
        $this->rdel('juris_data');
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>权限字典大全</title>
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
                        <p>温馨提示：权限字典大全，用于权限管理，可根据权限名称、访问路径、备注、创建时间进行查询。</p>
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
                                <option value="name">权限名称</option>
                                <option value="path">访问路径</option>
                                <option value="comment">备注</option>
                                <option value="found_date">创建时间</option>
                            </select>
                        </div>
                        <div class="layui-input-inline" name="fieldTypeInput">
                            <div class="showSearch nameItem layui-hide">
                                <input type="text" name="name" class="layui-input" placeholder="请输入权限名称" />
                            </div>
                            <div class="showSearch pathItem layui-hide">
                                <input type="text" name="path" class="layui-input" placeholder="请输入访问路径" />
                            </div>
                            <div class="showSearch commentItem layui-hide">
                                <input type="text" name="comment" class="layui-input" placeholder="请输入备注信息" />
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
                    </div>
                    <table id="juris_data" lay-filter="juris_data"></table>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script type="text/html" id="juris_dataTool">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">
            <i class="layui-icon layui-icon-add-1"></i>
            <span>新增字典</span>
        </button>
        <button class="layui-btn layui-btn-sm layui-btn-plug-danger" lay-event="Del">
            <i class="layui-icon layui-icon-delete"></i>
            <span>删除字典</span>
        </button>
    </div>
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
        elem: "#juris_data",
        url: api.url('data'),
        page: true,
        title: "权限字典大全",
        toolbar: "#juris_dataTool",
        skin: "line",
        where: where(),
        cols: [
            [{
                type: 'checkbox'
            }, {
                field: 'name',
                title: '权限名称',
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
                field: 'comment',
                title: '备注',
                width: 170,
                sort: true
            }, {
                field: 'found_date',
                title: '创建时间',
                minWidth: 170,
                sort: true
            }, {
                field: 'caozuo',
                title: '操作',
                templet: '#caozuoDelTool',
                width: 200,
                fixed: 'right',
                align: 'center'
            }]
        ]
    });

    table.on("toolbar(juris_data)", function(obj) {
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
    table.on('tool(juris_data)', function(obj) {
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
    laydate.render({
        elem: '[name=found_date]',
        type: 'date',
        done: function(value, date, endDate) {
            reload('juris_data');
        }
    });

    function caozuo_edit(obj) {
        layer.open({
            type: 2,
            title: '修改字典',
            area: ["550px", "420px"],
            maxmin: false,
            content: "juris_edit.php?id=" + obj.data.id,
            shade: 0.3
        });
    }

    function caozuo_del(obj) {
        layer.confirm('确定删除此字典吗？', function() {
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



    function add() {
        layer.open({
            type: 2,
            title: '新增字典',
            area: ["550px", "420px"],
            maxmin: false,
            content: "juris_edit.php",
            shade: 0.3
        });
    }

    function Del(checkStatus) {
        layer.confirm('确定删除选中的字典吗？', function() {
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
                        reload('juris_data');
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