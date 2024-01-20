<?php
include '../php/api.php';
class _web extends _api
{
    //获取软件列表
    public function _data()
    {
        $u = $this->server . '/php/list.php';
        $p = intval($this->is('page', 1));
        $l = intval($this->is('limit', 10));
        $res = $this->curl($u, ['page' => $p, 'limit' => $l, 'name' => $this->is('name')]);
        $d = $r = [];
        $q = 'SELECT `id`,`plug_id` FROM  `plug_id`';
        $re = $this->run($q);
        if ($re->num_rows > 0) {
            while ($row = $re->fetch_assoc()) {
                $r[] = $row;
            }
        }
        if ($res['icon'] != 1) {
            $j = ['code' => 0, 'count' => 0, 'data' => [], 'icon' => 1, 'page' => $p, 'limit' => $l, 'ip' => $res['data'], 'msg' => $res['code']];
            $this->res($j);
        }
        foreach ($res['data'] as $k) {
            $d[] = [
                'icon' => $k['icon'],
                'name' => $k['name'],
                'describe' => $k['describe'],
                'id' => $this->_Info($k, $r),
                'method' => $k['method'],
                'install_id' => $k['id'],
                'width' => $k['width'],
                'height' => $k['height'],
                'found_date' => $k['found_date'],
                'title' => $k['title'],
                'maxmin' => $k['maxmin']
            ];
        }
        $c = $res['count'];
        $j = ['code' => 0, 'count' => $c, 'data' => $d, 'icon' => 1, 'page' => $p, 'limit' => $l];
        $this->res($j);
    }


    //获取已安装插件的ID
    public function _Info($k, $a)
    {
        foreach ($a as $v) {
            if ($v['plug_id'] == $k['id']) {
                return $v['id'];
            }
        }
        return false;
    }

    //安装插件
    public function _install()
    {
        $this->ajax(['install_id']);
        $id = $_REQUEST['install_id'];
        $u = $this->server . '/php/api.php?eventType=install&id=' . $id;
        $c = $this->curl($u);
        if ($c['icon'] != 1) {
            $this->res($c['code'], $c['icon']);
        }
        $f =  $c['data']['code'];
        $d = "../bin/{$c['data']['method']}";
        if (is_dir($d)) {
            $this->res('请卸载相同功能的软件后再安装此软件', 3);
        }
        mkdir($d);
        $n = "{$d}/index.zip";
        $v = file_get_contents($f);
        file_put_contents($n, $v);
        $zip = new ZipArchive();
        $openRes = $zip->open($n);
        if ($openRes === true) {
            $zip->extractTo("../bin/{$c['data']['method']}");
            $zip->close();
            unlink($n);
            $this->db->add('plug_id', [
                'plug_id' => $c['data']['id']
            ]);
            $this->res('安装成功', 1);
        }
        $this->res('无法安装此软件', 3);
    }

    //卸载插件
    public function _del()
    {
        $this->rdel('plug_id', function ($d) {
            $f = "../bin/{$d['method']}";
            $this->delFile($f);
        }, '卸载成功', '卸载失败');
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>软件市场</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .icon {
            width: 20px;
            height: 20px;
            margin: 0px 5px;
            vertical-align: middle;
            margin-top: -5px;
        }

        .search-btn {
            border-radius: 0;
            background-color: transparent;
            color: #4d5259;
            padding: 0px 20px;
            height: 25px;
            line-height: 25px;
            margin-right: 5px;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-btn:hover {
            color: var(--color);
            opacity: 1;
        }

        .search-btn.this {
            background-color: var(--color);
            color: #ffffff;
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
                        <p>温馨提示：您可以通过安装插件来增加网站的功能，但是请注意，安装插件可能会导致网站出现故障，如果您不了解插件的功能，请不要安装插件。</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-form-item">
                        <label class="layui-form-label">软件名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" class="layui-input" placeholder="搜索软件名称" />
                        </div>
                        <button class="layui-btn layui-btn-sm layui-btn-normal search">
                            <i class="layui-icon layui-icon-search"></i>
                            <span>查询</span>
                        </button>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">软件分类</label>
                        <div class="layui-input-bl">
                            <button type="button" class="layui-btn layui-btn-sm search-btn this" value="">全部</button>
                            <button type="button" class="layui-btn layui-btn-sm search-btn" value="短信">短信</button>
                            <button type="button" class="layui-btn layui-btn-sm search-btn" value="验证码">验证码</button>
                            <button type="button" class="layui-btn layui-btn-sm search-btn" value="支付">支付</button>
                            <button type="button" class="layui-btn layui-btn-sm search-btn" value="二维码">二维码</button>
                        </div>

                    </div>
                    <table id="plug_list" lay-filter="plug_list"></table>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    table.render({
        elem: "#plug_list",
        url: api.url('data'),
        page: true,
        title: "网站用户",
        skin: "line",
        where: where(),
        cols: [
            [{
                field: 'name',
                title: '软件名称',
                width: 250,
                sort: true,
                templet: d => {
                    return '<img src="' + d.icon + '" class="icon" /><span>' + d.name + '</span>';
                }
            }, {
                field: 'describe',
                title: '说明',
                width: 450,
                sort: true
            }, {
                field: 'method',
                title: '服务名称',
                width: 150,
                sort: true
            }, {
                field: 'found_date',
                title: '上线时间',
                minWidth: 170,
                sort: true
            }, {
                field: 'caozuo',
                title: '操作',
                templet: d => {
                    return d.id ? `<a class='layui-table-link' lay-event='caozuo_edit'>
                                        <i class="layui-icon layui-icon-set"></i>
                                        <span>设置</span>
                                    </a>
                                    <span class='layui-table-divide'></span>
                                    <a class='layui-table-del' lay-event='caozuo_del'>
                                        <i class="layui-icon layui-icon-delete"></i>
                                        <span>卸载</span>
                                    </a>` : `<a class='layui-table-link' lay-event='caozuo_install'>
                                        <i class="layui-icon layui-icon-add-circle"></i>
                                        <span>安装</span>
                                    </a>`;
                },
                width: 250,
                fixed: 'right',
                align: 'center'
            }]
        ]
    });


    $(".search-btn").click(function() {
        $(".search-btn").removeClass("this");
        $(this).addClass("this");
        $('[name=name]').val($(this).val());
        reload("plug_list");
    });

    table.on("toolbar(plug_list)", function(obj) {
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

    function caozuo_edit(obj, el) {
        layer.open({
            type: 2,
            title: obj.data.title == '' ? false : obj.data.title,
            area: [obj.data.width, obj.data.height],
            maxmin: obj.data.maxmin == '1' ? true : false,
            content: "../bin/" + obj.data.method + "/index.php",
            shade: 0.3,
            success: function() {
                //$(el).addClass('layui-hide');
                // $(el).next().addClass('layui-hide');
            },
            cancel: function() {
                //$(el).removeClass('layui-hide');
                //$(el).next().removeClass('layui-hide');
            }
        });
    }

    function caozuo_del(obj) {
        layer.confirm('确定卸载此软件吗？', function() {
            var arr = [];
            arr[0] = obj.data;
            $.ajax({
                url: 'plug_list.php?method=del',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    layer.msg('正在卸载', {
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
                        reload("plug_list");
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }

    function caozuo_install(obj) {
        layer.confirm('确定安装此软件吗？', function() {
            $.ajax({
                url: 'plug_list.php?method=install',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    layer.msg('正在安装', {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    });
                },
                data: {
                    install_id: obj.data.install_id
                },
                success: function(data) {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                    if (data.code == '1') {
                        reload("plug_list");
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }

    table.on('tool(plug_list)', function(obj) {
        var data = obj.data;
        switch (obj.event) {
            case 'caozuo_edit':
                caozuo_edit(obj, this);
                break;
            case 'caozuo_del':
                caozuo_del(obj);
                break;
            case 'caozuo_install':
                caozuo_install(obj);
                break;
        };
    });
</script>

</html>