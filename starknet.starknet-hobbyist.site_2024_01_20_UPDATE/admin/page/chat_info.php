<?php
include '../php/api.php';
class _web extends _api
{
    //网页初始化
    public function _init()
    {
        $id = $this->is('user_to');
        $f = "*,
        (SELECT `name` FROM `roles_list` WHERE roles_list.id = user_data.roles_id) as `roles_name`";
        $sql = "SELECT {$f} FROM  `user_data` WHERE `id` = {$id};";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $this->info = $res->fetch_assoc();
            $this->isUser = $this->_getUser($id);
            $this->show = $this->is('show');
            return true;
        }
        $this->res('用户不存在', 3);
    }

    //获取是否是好友关系
    public function _getUser($id)
    {
        $sql = "SELECT id FROM  `chat_user` WHERE `user_id` = {$this->id} AND `user_to` = {$id};";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            return true;
        }
        return false;
    }

    //提交添加好友申请
    public function _add()
    {
        $this->ajax(['user_to', 'value']);
        $user_to = $_REQUEST['user_to'];
        if ($user_to == $this->id) {
            $this->res('不能添加自己为好友', 3);
        }
        $content = $_REQUEST['value'];
        $sql = "SELECT `state` FROM  `chat_add` WHERE `user_id` = {$this->id} AND `user_to` = {$user_to} AND `type` = 0;";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if ($row['state'] == '0') {
                $this->res('已发送申请', 1);
            }
            if ($row['state'] == '1') {
                $this->res('已经是好友关系', 1);
            }
        }
        $found_date = time();
        $sql = "INSERT INTO `chat_add` (`user_id`, `user_to`,`type`,`content`,`found_date`) VALUES ('{$this->id}', '{$user_to}','0','{$content}','{$found_date}'),('{$user_to}','{$this->id}', '1','{$content}','{$found_date}');";
        $this->sendMessage($user_to, 0, $this->user['picture'], "收到一条新的好友申请", $content, 2);
        $this->run($sql, false, '已提交好友申请', '提交失败');
    }
}
$web = new _web(2, "id,picture");
$web->method();
$web->_init();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>用户信息</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .info-head {
            display: flex;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 30px;
        }

        .info-user {
            width: calc(100% - 70px);
        }

        .info-picture {
            width: 70px;
            height: 80px;
            position: relative;
        }

        .info-picture>img {
            width: 70px;
            height: 70px;
            border-radius: 100%;
            margin: auto;
        }

        .official-1 {
            width: 20px;
            height: 20px;
            position: absolute;
            right: 0px;
            bottom: 10px;
            background-size: 100% 100%;
            background-image: url(../images/official.png);
        }


        .info-username {
            font-size: 20px;
            font-weight: bold;
            padding-top: 10px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
            padding-left: 15px;
        }

        .info-explain {
            color: #999;
            font-size: 14px;
            margin-top: 5px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
            padding-left: 15px;
        }

        .layui-form-label {
            text-align: left;
        }

        .add {
            background-color: #ffffff;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            text-align: center;
            padding: 20px;
        }

        .layui-text {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
        }
    </style>
</head>

<body class="layui-form">
    <div class="info-head">
        <div class="info-picture">
            <img src="<?php echo $web->info['picture'] != '' ? $web->info['picture'] : '../images/picture.png' ?>" alt="">
            <div class="official-<?php echo $web->info['admin'] ?>"></div>
        </div>
        <div class="info-user">
            <div class="info-username">
                <?php echo $web->info['nickname'] != '' ? $web->info['nickname'] : $web->info['username']; ?>
            </div>
            <div class="info-explain"><?php echo $web->info['explain'] != '' ? $web->info['explain'] : '对方很懒，啥也没留'; ?></div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <i class="layui-icon layui-icon-cellphone"></i>
            <span>手机号：</span>
        </label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-word-aux">
                <?php echo $web->info['tel'] ? $web->info['tel'] : '未填写'; ?>
            </div>
        </div>
    </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <i class="layui-icon layui-icon-email"></i>
            <span>邮箱：</span>
        </label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-word-aux">
                <?php echo $web->info['email'] ? $web->info['email'] : '未填写'; ?>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <i class="layui-icon layui-icon-time"></i>
            <span>注册于：</span>
        </label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-word-aux">
                <?php echo $web->info['found_date'] ? $web->info['found_date'] : '-'; ?>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <i class="layui-icon layui-icon-username"></i>
            <span>职位：</span>
        </label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-word-aux">
                <?php echo $web->info['roles_name'] ? $web->info['roles_name'] : '未设置'; ?>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <i class="layui-icon layui-icon-location"></i>
            <span>住址：</span>
        </label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-word-aux" title="<?php echo $web->info['street'] != '' ? $web->info['street'] : '未填写'; ?>">
                <?php echo $web->info['street'] != '' ? $web->info['street'] : '未填写'; ?>
            </div>
        </div>
    </div>
    <div class="add">
        <button type="button" class="layui-btn layui-btn-fluid layui-btn-normal <?php echo $web->isUser ? 'msg-btn' : 'add-btn'; ?>"><?php echo $web->isUser ? '发消息' : '添加好友'; ?></button>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=20201111001"></script>
<script>
    form.on("submit(submit)", function(data) {
        $.ajax({
            url: api.url('set'),
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
    $(".msg-btn").click(function() {
        var title = "<?php echo $web->info['username']; ?>";
        var id = "<?php echo $web->info['id']; ?>";
        var show = "<?php echo $web->show; ?>";
        if (show != '') {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
            return true;
        }
        parent.layer.open({
            type: 2,
            title: '聊天会话',
            content: 'page/chat_msg.php?user_to=' + id,
            area: ['600px', '550px'],
            anim: 5,
            maxmin: false,
            shadeClose: true,
            success: function() {
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
            }
            //scrollbar: false
        });
    });
    $(".add-btn").click(function() {
        parent.layer.prompt({
            title: '发起申请',
            formType: 0,
            value: '我是',
            success: function(el) {
                el.find('.layui-layer-input').attr('placeholder', '附加消息');
            }
        }, function(value, index) {
            var id = "<?php echo $web->info['id']; ?>";
            add(id, value, index);
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        });
    });

    function add(id, value, index) {
        $.ajax({
            url: api.url('add'),
            type: 'POST',
            dataType: 'json',
            data: {
                user_to: id,
                value: value
            },
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
                });
                if (data.code == 1) {
                    parent.layer.close(index);
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });

    }
</script>

</html>