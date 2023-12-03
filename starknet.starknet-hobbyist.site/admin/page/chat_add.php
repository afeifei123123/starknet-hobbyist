<?php
include '../php/api.php';
class _web extends _api
{
    public function _data()
    {
        $f = "`content`,`found_date`,`id`,`user_id`,`user_to`,
        (SELECT `picture` FROM `user_data` WHERE user_data.id = chat_add.user_id limit 1) AS `picture`,
        (SELECT `username` FROM `user_data` WHERE user_data.id = chat_add.user_id limit 1) AS `username`";
        $where = "`user_to` = {$this->id} AND `type` = 0 AND `state` = 0";
        $sql = "SELECT {$f} FROM  `chat_add` WHERE {$where} ORDER BY `id` DESC;";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['found_date'] = date('Y-m-d H:i:s', $row['found_date']);
                $data[] = $row;
            }
        }
        $this->res('调试成功', 1, $data);
    }

    //添加对方为好友
    public function _add()
    {
        $this->ajax(['user_to']);
        $user_to = $_REQUEST['user_to'];
        $sql = "UPDATE `chat_add` SET `state` = 1 WHERE `user_to` = '{$this->id}' AND `user_id` = {$user_to};";
        $this->run($sql);
        $sql = "UPDATE `chat_add` SET `state` = 1 WHERE `user_to` = '{$user_to}' AND `user_id` = {$this->id};";
        $this->run($sql);
        $sql = "INSERT INTO `chat_user` (`user_id`, `user_to`) VALUES ('{$user_to}', '{$this->id}'),('{$this->id}', '{$user_to}');";
        $this->sendMessage($user_to, 0, $this->user['picture'], "同意了你的好友申请", $this->id, 3);
        $this->run($sql, false, '添加好友成功', '添加好友失败');
    }

    //拒绝添加为好友
    public function _del()
    {
        $this->ajax(['user_to']);
        $user_to = $_REQUEST['user_to'];
        $sql = "UPDATE `chat_add` SET `state` = 2 WHERE `user_to` = '{$this->id}' AND `user_id` = {$user_to};";
        $this->run($sql);
        $sql = "UPDATE `chat_add` SET `state` = 2 WHERE `user_to` = '{$user_to}' AND `user_id` = {$this->id};";
        $this->sendMessage($user_to, 0, $this->user['picture'], "拒绝了你的好友请求", $this->id, 4);
        $this->run($sql, false);
    }
}
$web = new _web(2, 'id,picture');
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>验证消息</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        body {
            overflow: hidden;
            padding: 0px 15px;
        }

        .layui-tab {
            margin: 0px;
        }

        .user-item {
            height: 50px;
            display: flex;
            border-bottom: 1px solid #e8e8e8;
            cursor: pointer;
            padding: 5px 15px;
            position: relative;
        }

        .user-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .user-picture {
            width: 50px;
            height: 50px;
            display: flex;
            cursor: pointer;
        }

        .user-picture>img {
            width: 40px;
            height: px;
            border-radius: 100%;
            margin: auto;
        }

        .user-info {
            width: calc(100% - 50px);
        }

        .user-name {
            font-size: 14px;
            color: #333;
            padding-left: 5px;
            margin-top: 5px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
        }

        .user-explain {
            padding-left: 5px;
            font-size: 12px;
            color: #999;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
            margin-top: 5px;
        }

        .user-explain img {
            width: 10px;
            height: 10px;
        }

        .user-nickname {
            color: #999;
            float: right;
        }

        .user-explain>button {
            float: right;
            margin-right: 5px;
        }

        .not::before {
            content: "";
            width: 80px;
            height: 80px;
            display: block;
            margin: 40px auto 20px auto;
            background-image: url(../images/notice-not.png);
            background-repeat: no-repeat;
            background-size: 100% auto;
        }

        .not::after {
            content: "啥也没有";
            display: block;
            text-align: center;
            color: #c2c2c2;
        }
    </style>
</head>

<body class="layui-form">
    <div class="layui-tab layui-tab-brief">
        <ul class="layui-tab-title">
            <li class="layui-this">好友申请</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show list"></div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    $(document).on("click", ".add", function() {
        var el = this;
        parent.layer.confirm('确定添加对方为您的好友吗？', function(index) {
            parent.layer.close(index);
            add(el, index);
        });
    });
    $(document).on("click", ".del", function() {
        var el = this;
        parent.layer.confirm('确定拒绝对方添加你为好友吗？', function(index) {
            parent.layer.close(index);
            del(el, index);
        });
    });

    $(document).on('click', '.user-picture', function() {
        var id = $(this).parent().attr('data-id');
        parent.layer.open({
            type: 2,
            title: false,
            content: 'page/chat_info.php?user_to=' + id + '&show=true',
            area: ['336px', '450px'],
            anim: 5,
            maxmin: false,
            shadeClose: true,
            //scrollbar: false
        });
    });


    function add(elem, index) {
        $.ajax({
            url: api.url('add'),
            type: 'POST',
            dataType: 'json',
            data: {
                user_to: $(elem).parents('.user-item').attr('data-id')
            },
            beforeSend: function() {
                parent.layer.msg("正在添加", {
                    icon: 16,
                    shade: 0.2,
                    time: false
                });
            },
            success: function(data) {
                parent.layer.msg(data.msg, {
                    icon: data.code
                });
                if (data.code == 1) {
                    init();
                    var list = parent.$("iframe[src='page/chat_user.php']");
                    if (list.length > 0) {
                        list[0].contentWindow.window.ChatUser();
                    }

                    var c = parent.$("iframe[src='page/chat_user.php']");
                    if (c.length > 0) {
                        c[0].contentWindow.window.ChatAdd();
                    }
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }

    function del(elem, index) {
        $.ajax({
            url: api.url('del'),
            type: 'POST',
            dataType: 'json',
            data: {
                user_to: $(elem).parents('.user-item').attr('data-id')
            },
            beforeSend: function() {
                parent.layer.msg("正在删除", {
                    icon: 16,
                    shade: 0.2,
                    time: false
                });
            },
            success: function(data) {
                parent.layer.msg(data.msg, {
                    icon: data.code
                });
                if (data.code == 1) {
                    init();
                    var c = parent.$("iframe[src='page/chat_user.php']");
                    if (c.length > 0) {
                        c[0].contentWindow.window.ChatAdd();
                    }
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }

    window.init = function() {
        $.ajax({
            url: api.url('data'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.code == 1) {
                    var elem = $('.list');
                    var li = $('.layui-tab-title>li').eq(0);
                    elem.html('');
                    for (var key in data.data) {
                        var json = data.data[key];
                        var item = `<li class="user-item" data-id="${json.user_id}">
                    <div class="user-picture">
                        <img src="${json.picture}">
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            <span>${json.username}</span>
                            <span class="user-nickname">${json.found_date}</span>
                        </div>
                        <div class="user-explain">
                            <span>附加消息：${json.content}</span>
                            <button type="button" class="layui-btn layui-btn-xs layui-btn-normal add">同意</button>
                            <button type="button" class="layui-btn layui-btn-xs layui-btn-plug-danger del">拒绝</button>
                        </div>
                    </div>
                </li>`;
                        elem.append(item);
                    }
                    li.html(`好友申请(${data.data.length})`);
                    if (data.data.length == 0) {
                        elem.html('<div class="not"></div>');
                        li.html('好友申请');

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
    };
    init();
</script>

</html>