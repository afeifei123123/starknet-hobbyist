<?php
include '../php/api.php';
class _web extends _api
{
    public function _set()
    {
        $this->ajax(['out_password', 'password', 'confirm_password'], true);
        $u = $_REQUEST['out_password'];
        $p = $_REQUEST['password'];
        $c = $_REQUEST['confirm_password'];
        if ($u != $this->user['password']) {
            $this->res('旧密码填写不正确!', 3);
        }
        if ($p != $c) {
            $this->res('确认密码和密码填写不一致!', 3);
        }
        if ($u == $c) {
            $this->res('新密码不能与旧密码相同!', 3);
        }
        $sql = "UPDATE `user_data` SET `password` = '{$p}' WHERE `id` = {$this->user['id']};";
        $res = $this->run($sql);
        $n = $this->conn->affected_rows;
        $this->res($n >= 1 ? '密码修改成功' : '密码修改失败', $n >= 1 ? 1 : 3);
    }
}
$web = new _web(2, 'id,password');
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>修改密码</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>旧密码</span>
        </label>
        <div class="layui-input-block">
            <input type="password" name="out_password" class="layui-input" lay-verify="password" placeholder="请输入旧密码" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>新密码</span>
        </label>
        <div class="layui-input-block">
            <input type="password" name="password" class="layui-input" lay-verify="password" placeholder="请输入新密码" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>确认密码</span>
        </label>
        <div class="layui-input-block">
            <input type="password" name="confirm_password" class="layui-input" lay-verify="password" placeholder="请再次输入新的密码" />
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
    form.on('submit(submit)', d => {
        var b = $("[lay-filter='submit']");
        $.ajax({
            url: api.url('set'),
            type: 'POST',
            dataType: 'json',
            data: d.field,
            beforeSend: () => {
                parent.layer.msg('正在提交', {
                    icon: 16,
                    shade: 0.05,
                    time: false
                });
            },
            success: r => {
                parent.layer.msg(r.msg, {
                    icon: r.code
                }, () => {
                    if (r.code == 1) {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    }
                });
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
        return false;
    });
</script>

</html>