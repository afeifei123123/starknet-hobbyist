<?php
include "../php/api.php";
class _web extends _api
{
    public function _set()
    {
        $_REQUEST['user_id'] = $this->id;
        $sql = $this->getSql('user_action', ['id', 'user_id', 'name',  'url'], false, ['user_id', 'name']);
        $id = $this->is('id');
        $sql = $id != "" ? $sql['upd'] : $sql['add'];
        $this->run($sql, false);
    }

    public function init()
    {
        $sql = "SELECT * FROM `user_action` WHERE `user_id` = '{$this->id}';";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $this->info = $res->fetch_assoc();
        }
    }
};
$web = new _web(2, 'id', false, true);
$web->method();
$web->init();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>编辑网页</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>网页名称</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="name" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['name']) ? $web->info['name'] : ''; ?>" placeholder="请输入网页名称" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>网页地址</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="url" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['url']) ? $web->info['url'] : ''; ?>" placeholder="请输入网页地址" />
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
    layui.use(["jquery", "form"], function() {
        var $ = layui.$,
            form = layui.form;
        form.on("submit(submit)", function(data) {
            $.ajax({
                url: api.url('set') + "&id=<?php echo $web->is('id'); ?>",
                type: "POST",
                dataType: "json",
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
                            parent.action.init();
                        }
                    });
                },
                error: function(data) {
                    console.log(data);
                    layer.msg(data.responseText, {
                        icon: 5
                    });
                }
            });
            return false;
        });
    });
</script>

</html>