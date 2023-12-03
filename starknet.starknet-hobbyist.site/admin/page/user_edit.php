<?php
include '../php/api.php';
class _web extends _api
{
    public function _roles()
    {
        $sql = "select `id`,`name` from `roles_list` ORDER BY `indexs`,`id` ASC";
        $res = $this->run($sql);
        $d = [];
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $d[] = $row;
            }
        }
        $this->res('调试成功', 1, $d);
    }

    public function _set()
    {
        $sql = $this->getSql('user_data', ['id', 'username:username', 'password:password', 'roles_id:number', 'blacklist:number', 'sex:number', 'admin:number', 'comment']);
        $id = $this->is('id');
        $sql = $id != "" ? $sql['upd'] : $sql['add'];
        $this->run($sql, false);
    }
};
$web = new _web(2, 'id', false, true);
$web->method('user_data');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>修改用户信息</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must"></span>
            <span>用户ID</span>
        </label>
        <div class="layui-input-inline">
            <div class="layui-form-mid layui-word-aux">
                <span><?php echo isset($web->info['id']) ? $web->info['id'] : ''; ?></span>
                <span class="layui-table-divide"></span>
                <a class="layui-table-link" title="复制ID" lay-copy="<?php echo isset($web->info['id']) ? $web->info['id'] : ''; ?>">
                    <i class="layui-icon layui-icon-list"></i>
                    <span>复制ID</span>
                </a>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>用户名</span>
        </label>
        <div class="layui-input-inline">
            <input type="text" name="username" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['username']) ? $web->info['username'] : ''; ?>" placeholder="请输入用户名" />
        </div>
        <label class="layui-form-label">
            <span class="layui-must">*</span>
            <span>登录密码</span>
        </label>
        <div class="layui-input-inline">
            <input type="text" name="password" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['password']) ? $web->info['password'] : ''; ?>" placeholder="请输入登录密码" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span>角色</span>
        </label>
        <div class="layui-input-inline">
            <select name="roles_id">
                <option value=""></option>
            </select>
        </div>
        <label class="layui-form-label">
            <span>性别</span>
        </label>
        <div class="layui-input-inline">
            <input type="radio" name="sex" value="0" title="男" lay-filter="sex" <?php echo isset($web->info['sex']) ? ($web->info['sex'] == '0' ? 'checked' : '') : 'checked'; ?> />
            <input type="radio" name="sex" value="1" title="女" lay-filter="sex" <?php echo isset($web->info['sex']) ? ($web->info['sex'] == '1' ? 'checked' : '') : ''; ?> />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span>管理员</span>
        </label>
        <div class="layui-input-inline">
            <input type="radio" name="admin" value="1" title="开启" lay-filter="admin" <?php echo isset($web->info['admin']) ? ($web->info['admin'] == '1' ? 'checked' : '') : ''; ?> />
            <input type="radio" name="admin" value="0" title="关闭" lay-filter="admin" <?php echo isset($web->info['admin']) ? ($web->info['admin'] == '0' ? 'checked' : '') : 'checked'; ?> />
        </div>
        <div class="layui-form-mid layui-word-aux">
            <span class="layui-font-red">*</span>
            <span class="layui-font-red">开启后用户将拥有所有权限！</span>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span>黑名单</span>
        </label>
        <div class="layui-input-inline">
            <input type="radio" name="blacklist" value="1" title="开启" lay-filter="blacklist" <?php echo isset($web->info['blacklist']) ? ($web->info['blacklist'] == '1' ? 'checked' : '') : ''; ?> />
            <input type="radio" name="blacklist" value="0" title="关闭" lay-filter="blacklist" <?php echo isset($web->info['blacklist']) ? ($web->info['blacklist'] == '0' ? 'checked' : '') : 'checked'; ?> />
        </div>
        <div class="layui-form-mid layui-word-aux">
            <span class="layui-font-red">*</span>
            <span class="layui-font-red">开启后禁止用户登录和一切操作！</span>
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
    var app = {
        init() {
            var t = this;
            t.r(), t.s();
        },
        r() {
            $.ajax({
                url: api.url('roles'),
                type: 'POST',
                dataType: 'json',
                success: r => {
                    if (r.code != 1) return layer.msg(r.msg, {
                        icon: r.code
                    });
                    var e = $('[name=roles_id]');
                    for (var k in r.data) e.append(`<option value="${r.data[k].id}">${r.data[k].name}</option>`);
                    e.val('<?php echo isset($web->info['roles_id']) ? $web->info['roles_id'] : ''; ?>');
                    form.render();
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        },
        s() {
            form.on('submit(submit)', function(s) {
                $.ajax({
                    url: api.url('set') + "&id=<?php echo $web->is('id'); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: s.field,
                    beforeSend: () => parent.layer.msg('正在提交', {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    }),
                    success: r => {
                        parent.layer.msg(r.msg, {
                            icon: r.code
                        }, () => {
                            if (r.code == 1) {
                                var i = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(i);
                                parent.reload('user_data');
                            }
                        });
                    },
                    error: r => layer.alert(r.responseText, {
                        icon: 2
                    })
                });
                return false;
            });
        }
    };
    app.init();
</script>

</html>