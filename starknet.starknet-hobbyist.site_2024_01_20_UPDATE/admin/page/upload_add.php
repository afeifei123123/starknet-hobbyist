<?php
include '../php/api.php';
class _web extends _api
{
    //初始化
    public function _init()
    {
        $dir = "../upload";
        $_REQUEST['path'] = $dir . $this->is('path');
        $_REQUEST['local'] = $dir . $this->is('local');
    }

    //新增文件或者目录
    public function _add()
    {
        $this->_init();
        $this->ajax(["name", "path", "type"]);
        $path = $_REQUEST["path"];
        $name = $_REQUEST["name"];
        $type = $_REQUEST["type"];
        $file = iconv("utf-8", "gb2312//IGNORE", $path . $name);
        if ($type == "dir") {
            if (is_dir($file)) {
                $this->res("目录已存在", "3");
            }
            $res = mkdir($file, 0777, true);
            if (!$res) {
                $this->res("目录创建失败", "5");
            }
            $this->res("目录创建成功", "1");
        } else {
            if (is_file($file)) {
                $this->res("文件已存在", "3");
            }
            $res = file_put_contents($file, "");
            $this->res("文件创建成功", "1");
        }
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <title>
        新增文件或者目录
    </title>
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>">
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        body {
            background-color: #FFFFFF;
            padding-right: 0px;
        }
    </style>
</head>

<body class="layui-form form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            文件类型
            <span class="layui-must">*</span>
        </label>
        <div class="layui-input-block">
            <input type="radio" name="type" value="dir" title="文件夹" checked>
            <input type="radio" name="type" value="file" title="文本文件">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            文件名称
            <span class="layui-must">*</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="name" class="layui-input" lay-verify="required">
        </div>
    </div>
    <div class="layui-footer layui-nobox">
        <button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">确定</button>
        <button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    $("[name=name]").focus();
    form.on("submit(submit)", function(data) {
        data.field["path"] = '<?php echo $web->is('path'); ?>';
        $.ajax({
            url: api.url('add'),
            type: 'POST',
            dataType: 'json',
            data: data.field,
            beforeSend: function() {
                layer.msg("正在提交", {
                    icon: 16,
                    shade: 0.05,
                    time: false
                });
            },
            success: function(data) {
                if (data.code == 1) {
                    if (window.parent.frames.reload != undefined) {
                        window.parent.frames.reload("file");
                        parent.layer.closeAll();
                        parent.layer.msg(data.msg, {
                            icon: data.code
                        });
                    }
                    if (window.parent.frames.nav != undefined) {
                        window.parent.frames.nav();
                        parent.layer.closeAll();
                        parent.layer.msg(data.msg, {
                            icon: data.code
                        });
                    }
                } else {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
        return false;
    });
    $(".layui-input").keydown(function(e) {
        if (e.keyCode == 13) {
            $("[lay-filter=submit]").trigger("click");
        }
    });
</script>

</html>