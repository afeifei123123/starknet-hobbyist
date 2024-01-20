<?php
include '../php/api.php';
class _web extends _api
{
    public function _set()
    {
        $_REQUEST['method'] = $this->is('method1');
        $sql = $this->getSql('juris_data', ['id', 'name', 'path', 'method', 'comment'], false, ['name']);
        $id = $this->is('id');
        $sql = $id != "" ? $sql['upd'] : $sql['add'];
        $this->run($sql, false);
    }
};
$web = new _web(2, "id", false, true);
$web->method('juris_data');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>修改权限字典</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>权限名称</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="name" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['name']) ? $web->info['name'] : ''; ?>" placeholder="请输入权限名称" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>访问路径</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="path" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['path']) ? $web->info['path'] : ''; ?>" placeholder="请输入访问路径"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>方法名称</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="method1" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['method']) ? $web->info['method'] : ''; ?>" placeholder="请输入方法名称"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span>备注信息</span>
        </label>
        <div class="layui-input-block">
            <textarea name="comment" class="layui-textarea" placeholder="请输入备注信息"><?php echo isset($web->info['comment']) ? $web->info['comment'] : ''; ?></textarea>
        </div>
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
            url: api.url('set') + "&id=<?php echo $web->is('id'); ?>",
            type: 'POST',
            dataType: 'json',
            data: data.field,
            beforeSend: function() {
                parent.layer.msg("正在提交", {
                    icon: 16,
                    shade: 0.05,
                    time: false
                });
            },
            success: function(data) {
                parent.layer.msg(data.msg, {
                    icon: data.code
                }, function() {
                    if (data.code == 1) {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.reload('juris_data');
                    }
                });
            },
            error: r => layer.alert(r.responseText, { icon: 2 })
        });
        return false;
    });
</script>

</html>