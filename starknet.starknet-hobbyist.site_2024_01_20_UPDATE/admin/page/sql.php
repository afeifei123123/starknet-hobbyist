<?php
include '../php/api.php';
class _web extends _api
{
    //获取所有表
    public function _surface()
    {
        $data = [];
        $sql = "show table status";
        $result = $this->run($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    "name" => explode(":", $row["Comment"])[0],
                    "value" => $row["Name"]
                ];
            }
        }
        $this->res('获取表成功', 1, $data);
    }

    //获取字段
    public function _field()
    {
        $this->ajax(['surface']);
        $s = $_REQUEST["surface"];
        if (
            $s == ''
        ) {
            $this->res('请选择表', 3);
        }
        $sql = "SHOW FULL COLUMNS FROM {$s};";
        $result = $this->run($sql);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    "name" => explode(":", $row["Comment"])[0],
                    "value" => $row["Field"],
                    "type" => explode("(", $row["Type"])[0]
                ];
            }
        }
        $this->res('查询字段成功', 1, $data);
    }

    //生成sql
    public function _submit()
    {
        $this->ajax(['surface', 'type']);
        $s = $_REQUEST["surface"];
        $t = $_REQUEST["type"];
        $f = $this->is('query', '*');
        $a = $this->is('where', false);
        $where = !$a ? '' : $this->_getWhere($a);
        $q = "SELECT {$t}({$f}) FROM `{$s}`{$where};";
        $sql = $this->getInSql($q);
        $res = $this->run($sql);
        if (!$res) {
            $this->res('SQL语法错误，请进行修复', 3);
        }
        $this->res('生成成功', 1, ['sql' => $q]);
    }

    //生成条件
    public function _getWhere($a)
    {
        $w = "";
        foreach ($a as $k) {
            $n = $k["name"];
            $t = intval($k["type"]);
            $v = $k["value"];
            switch ($t) {
                case 0:
                    $w .= "`{$n}` = '{$v}' AND ";
                    break;
                case 1:
                    $w .= "`{$n}` LIKE '%{$v}%' AND ";
                    break;
                case 2:
                    $w .= "`{$n}` LIKE '%{$v}' AND ";
                    break;
                case 3:
                    $w .= "`{$n}` LIKE '{$v}%' AND ";
                    break;
                case 4:
                    $w .= "`{$n}` > '{$v}' AND ";
                    break;
                case 5:
                    $w .= "`{$n}` >= '{$v}' AND ";
                    break;
                case 6:
                    $w .= "`{$n}` < '{$v}' AND ";
                    break;
                case 7:
                    $w .= "`{$n}` <= '{$v}' AND ";
                    break;
                case 8:
                    $w .= "`{$n}` != '{$v}' AND ";
                    break;
            };
        }
        $w = substr($w, 0, strlen($w) - 5);
        return " where " . $w;
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>数据库可视化查询</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>选择表</span>
        </label>
        <div class="layui-input-inline">
            <select name="surface" lay-verify="required" lay-filter="surface" lay-search>
                <option value=""></option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>结果类型</span>
        </label>
        <div class="layui-input-inline">
            <select name="type" lay-verify="required" lay-filter="type" lay-search>
                <option value="COUNT">计数</option>
                <option value="SUM">求和</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">查询字段</label>
        <div class="layui-input-block query">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">条件字段</label>
        <div class="layui-input-block" id="field-box">
        </div>
    </div>
    <div class="where-box">
        <!-- 条件字段 -->
    </div>
    <div class="layui-footer layui-nobox">
        <button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">保存</button>
        <button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    form.on("submit(submit)", function(data) {
        $.ajax({
            url: api.url('submit'),
            type: 'POST',
            dataType: 'json',
            data: {
                surface: data.field.surface,
                type: data.field.type,
                query: data.field.query,
                where: getWhere()
            },
            beforeSend: function() {
                layer.msg("正在生成", {
                    icon: 16,
                    shade: 0.2,
                    time: false
                });
            },
            success: function(data) {
                layer.msg(data.msg, {
                    icon: data.code
                }, function() {
                    if (data.code == 1) {
                        parent.phpm.val(data.data.sql);
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    }
                });
            },
            error: r => layer.alert(r.responseText, { icon: 2 })
        });
        return false;
    });

    function getWhere() {
        var input = $(".where-box").find(".layui-form-item");
        var arr = [];
        input.each(function() {
            var name = $(this).attr("title");
            var type = $(this).find(".layui-input-inline").eq(0).find("select").val();
            var value = $(this).find(".layui-input-inline").eq(1).find("input").val();
            var json = {
                name: name,
                type: type,
                value: value
            }

            arr.push(json);
        });
        return arr;
    }
    class _api {
        constructor() {
            var seft = this;
            form.on('select(surface)', function(data) {
                seft.field(data.value);
            });
            form.on('checkbox(field-where)', function(data) {
                var name = $(data.elem).attr("title");
                var value = data.value;
                var box = $(".where-box");
                if (data.elem.checked == true) {
                    var item = '<div class="layui-form-item ' + value +
                        '-item" title="' + value + '"><label class="layui-form-label">' + name +
                        '</label><div class="layui-input-inline"><select lay-verify="required"><option value="0">等于</option><option value="1">包含</option><option value="2">前面包含</option><option value="3">后面包含</option><option value="4">大于</option><option value="5">大于或等于</option><option value="6">小于</option><option value="7">小于或等于</option><option value="8">不等于</option></select></div><div class="layui-input-inline"><input type="text" lay-verify="required" class="layui-input" value="{$' +
                        value + '}"/></div></div>';
                    box.append(item);
                } else {
                    box.find("." + value + "-item").remove();
                }
                form.render();
            });
        }

        init() {
            $.ajax({
                url: api.url('surface'),
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    layer.msg("正在加载", {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    });
                },
                success: function(data) {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                    if (data.code == 1) {
                        var elem = $("[name=surface]");
                        elem.html('<option value=""></option>');
                        if (data.code == 1) {
                            for (var key in data.data) {
                                var json = data.data[key];
                                var item = '<option value="' + json.value + '">' + json.name + '[' +
                                    json.value + ']</option>';
                                elem.append(item);
                            }
                            form.render();
                        }
                    }
                },
                error: r => layer.alert(r.responseText, { icon: 2 })
            });
        }

        field(surface) {
            $.ajax({
                url: api.url('field'),
                type: 'POST',
                dataType: 'json',
                data: {
                    surface: surface
                },
                beforeSend: function() {
                    layer.msg("正在加载", {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    });
                },
                success: function(data) {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                    if (data.code == 1) {
                        var elem = $('.query');
                        var box = $("#field-box");
                        box.html('');
                        elem.html('');
                        for (var key in data.data) {
                            var json = data.data[key];
                            var item = '<input type="radio" name="query" value="' + json.value +
                                '" title="' + json.name +
                                '">';
                            elem.append(item);
                            var checkbox = '<input type="checkbox" name="item" title="' + json.name +
                                '" value="' + json.value +
                                '" lay-skin="primary" lay-filter="field-where" />';
                            box.append(checkbox);
                        }
                        form.render();
                        $(".where-box").html('');
                    }
                },
                error: r => layer.alert(r.responseText, { icon: 2 })
            });

        }
    };
    var App = new _api();
    App.init();
</script>

</html>