<?php
include '../php/api.php';
class _web extends _api
{
    //获取联系人列表
    public function _ChatUser()
    {
        $data = [];
        $f = "`id`,`user_to`,`found_date`,`name`,
        (SELECT `picture` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `picture`,
        (SELECT `username` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `username`,
        (SELECT `explain` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `explain`,
        (SELECT `token` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `token`,
        (SELECT `nickname` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `nickname`,
        (SELECT `state` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `state`,
        (SELECT `admin` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `admin`";
        $sql = "SELECT {$f} FROM  `chat_user` WHERE `user_id` = '{$this->id}' ORDER BY `indexs`,`id` ASC;";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        $this->res('调试成功', 1, $data);
    }

    //查询手机号用户
    public function _QueryUser()
    {
        $this->ajax(['tel']);
        $tel = $_REQUEST['tel'];
        $sql = "SELECT `id` FROM  `user_data` WHERE `username` = '{$tel}';";
        $res = $this->run($sql);
        if ($res->num_rows == 0) {
            $this->res('查询手机号用户不存在', 3);
        }
        $data = $res->fetch_assoc();
        $this->res('查询成功', 1, $data);
    }

    //查询消息
    public function _ChatMsg()
    {
        $data = [];
        $f = "`id`,`user_to`,`name`,
        (SELECT `picture` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `picture`,
        (SELECT `username` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `username`,
        (SELECT `content` FROM `chat_msg` WHERE chat_msg.user_to = chat_user.user_to ORDER BY `id` DESC LIMIT 1) AS `content`,
        (SELECT `found_date` FROM `chat_msg` WHERE chat_msg.user_to = chat_user.user_to ORDER BY `id` DESC LIMIT 1) AS `time`,
        (SELECT count(`read`) FROM `chat_msg` WHERE chat_msg.user_to = chat_user.user_to AND chat_msg.read = 0) AS `count`,
        (SELECT `state` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `state`,
        (SELECT `admin` FROM `user_data` WHERE user_data.id = chat_user.user_to limit 1) AS `admin`";
        $sql = "SELECT {$f} FROM  `chat_user` WHERE `user_id` = '{$this->id}';";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                if ($row['time'] != null) {
                    $data[] = $row;
                }
            }
        }
        $this->res('调试成功', 1, $data);
    }

    //删除好友
    public function _DelUser()
    {
        $this->ajax(['user_to']);
        $user_to = $_REQUEST['user_to'];
        //删除好友关系
        $sql = "DELETE FROM `chat_user` WHERE `user_id` = {$this->id} AND `user_to` = {$user_to};";
        $this->run($sql);
        $sql = "DELETE FROM `chat_user` WHERE `user_id` = {$user_to} AND `user_to` = {$this->id};";
        $this->run($sql);
        //删除之间的聊天记录
        $sql = "DELETE FROM `chat_msg` WHERE `user_id` = {$this->id} AND `user_to` = {$user_to};";
        $this->run($sql);
        $sql = "DELETE FROM `chat_msg` WHERE `user_id` = {$user_to} AND `user_to` = {$this->id};";
        $this->run($sql);
        //删除申请记录
        $sql = "DELETE FROM `chat_add` WHERE `user_id` = {$this->id} AND `user_to` = {$user_to};";
        $this->run($sql);
        $sql = "DELETE FROM `chat_add` WHERE `user_id` = {$user_to} AND `user_to` = {$this->id};";
        $this->sendMessage($user_to, 0, $this->user['picture'], "解除了好友关系", $this->id, 5);
        $this->run($sql, false);
    }

    //获取等待添加的好友数量
    public function _ChatAdd()
    {
        $sql = "SELECT COUNT(`id`) FROM  `chat_add` WHERE `user_to` = {$this->id} AND `type` = 0 AND `state` = 0;";
        $res = $this->run($sql);
        $count = 0;
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $count = intval($row['COUNT(`id`)']);
        }
        $this->res('调试成功', 1, ['count' => $count]);
    }

    //更新在线状态
    public function _setState()
    {
        $t = $this->is('type', '1');
        $sql = "UPDATE `user_data` SET `state` = '{$t}' WHERE `id` = {$this->id};";
        $this->run($sql, false);
    }

    //排序用户列表
    public function _SortUser()
    {
        $this->ajax(['data']);
        $d = $_REQUEST['data'];
        foreach ($d as $k) {
            $id = $k['id'];
            $i = $k['indexs'] + 1;
            $q = "UPDATE `chat_user` SET `indexs` = '{$i}' WHERE `user_id` = {$this->id} AND  `user_to` = {$id};";
            $res = $this->run($q);
            if (!$res) {
                $this->res('排序失败', 3);
            }
        }
        $this->res('排序成功', 1);
    }

    //修改备注名称
    public function _SetName()
    {
        $this->ajax(['user_to', 'name']);
        $id = intval($_REQUEST['user_to']);
        $name = $_REQUEST['name'];
        $q = "UPDATE `chat_user` SET `name` = '{$name}' WHERE `user_id` = {$this->id} AND  `user_to` = {$id};";
        $this->run($q, false);
    }
}
$web = new _web(2, "*");
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>实时通讯</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/chat.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div class="header">
        <div class="info">
            <div class="info-picture" data-id="<?php echo $web->id; ?>">
                <img src="<?php echo $web->user['picture']; ?>" alt="">
            </div>
            <div class="info-user">
                <div class="info-username">
                    <?php echo $web->user['nickname'] != '' ? $web->user['nickname'] : $web->user['username']; ?>
                </div>
                <div class="info-explain">
                    <?php echo $web->user['explain'] != '' ? $web->user['explain'] :  '未设置签名' ?>
                </div>
                <span class="state state-<?php echo $web->user['state']; ?>"></span>
            </div>
        </div>
    </div>
    <div class="body">
        <div class="layui-tab layui-tab-brief">
            <ul class="layui-tab-title">
                <li class="layui-this">消息</li>
                <li>联系人</li>
                <li>动态</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show msg">
                    <li class="msg-item">
                        <!-- 消息 -->
                    </li>
                </div>
                <div class="layui-tab-item user">
                    <!-- 联系人 -->
                </div>
                <div class="layui-tab-item space">
                    <div class="not"></div>
                </div>
            </div>
        </div>
        <div class="tool">
            <li class="tool-item list" title="验证消息">
                <img src="../images/add-list.png">
                <span class="layui-badge layui-hide">0</span>
            </li>
            <li class="tool-item add" title="添加好友"><img src="../images/add-user.png"></li>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/Sortable.min.js?v=<?php echo $web->v; ?>"></script>
<script>
    window.ChatUser = function() {
        $.ajax({
            url: api.url('ChatUser'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.code == 1) {
                    var elem = $('.user');
                    elem.html('');
                    for (var key in data.data) {
                        var json = data.data[key];
                        var name = json.name != '' ? json.name : json.username;
                        var item = `<li class="user-item" data-id="${json.user_to}">
                    <div class="user-picture state-${json.state}">
                        <img src="${json.picture}" />
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            <span>${name}</span>
                            <span class="user-nickname">(${json.nickname})</span>
                        </div>
                        <div class="user-explain" title="${json.explain}">${json.explain}</div>
                        <span class="state-${json.state}"></span>
                        <span class="admin-${json.admin}"></span>
                    </div>
                </li>`;
                        elem.append(item);
                    }
                    if (data.data.length == 0) {
                        elem.html('<div class="not"></div>');
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
    }


    window.ChatMsg = function() {
        $.ajax({
            url: api.url('ChatMsg'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.code == 1) {
                    var elem = $('.msg');
                    elem.html('');
                    for (var key in data.data) {
                        var json = data.data[key];
                        var username = json.name != '' ? json.name : json.username;
                        var item = `<li class="user-item" data-id="${json.user_to}">
                    <div class="user-picture state-${json.state}">
                        <img src="${json.picture}" />
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            <span>${username}</span>
                            <span class="user-nickname">${json.time != null ? json.time :''}</span>
                        </div>
                        <div class="user-explain">
                        <span>${json.content != null ? json.content.replace(/<img.*?(?:>|\/>)/g, '[图片]') : ''}</span>
                        <span class="layui-badge ${json.count > 0 ? '' : 'layui-hide'}">${json.count}</span>
                        <span class="state-${json.state}"></span>
                        <span class="admin-${json.admin}"></span>
                        </div>
                    </div>
                </li>`;
                        elem.append(item);
                    }
                    if (data.data.length == 0) {
                        elem.html('<div class="not"></div>');
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

    window.ChatAdd = function() {
        $.ajax({
            url: api.url('ChatAdd'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.code == 1) {
                    var elem = $('.tool-item.list').find('.layui-badge');
                    if (data.data.count == 0) {
                        elem.addClass('layui-hide').html('0');
                        return false;
                    }
                    elem.removeClass('layui-hide').html(data.data.count);
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });

    };

    function init() {
        ChatUser();
        ChatMsg();
        ChatAdd();
        $(document).on('click', '.user-item', function() {
            var id = $(this).attr('data-id');
            var title = $(this).find('.user-name').html();
            parent.layer.open({
                type: 2,
                title: '聊天会话',
                content: 'page/chat_msg.php?user_to=' + id,
                area: ['600px', '550px'],
                anim: 5,
                maxmin: false,
                shadeClose: true,
                //scrollbar: false
            });
        });
        $(".add").click(function() {
            parent.layer.prompt({
                title: '添加好友',
                formType: 0,
                success: function(el) {
                    el.find('.layui-layer-input').attr('placeholder', '请输入对方手机号/用户名');
                    el.find('.layui-layer-input').keydown(function(e) {
                        if (e.keyCode == 13) {
                            el.find('.layui-layer-btn0').click();
                        }
                    });
                }
            }, function(tel, index) {
                QueryUser(tel, index);
            });
        });
        $(document).on('click', '.info-picture', function() {
            var id = $(this).attr('data-id');
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
        $(document).on('click', '.list', function() {
            parent.layer.open({
                type: 2,
                title: '验证消息',
                content: 'page/chat_add.php',
                area: ['500px', '450px'],
                anim: 5,
                maxmin: false,
                shadeClose: true,
                //scrollbar: false
            });
        });
        api.menu('.user .user-item', function(el) {
            var seft = el;
            layui.dropdown.render({
                elem: el,
                show: true,
                trigger: 'contextmenu',
                data: [{
                        title: '查看资料',
                        id: 'info'
                    },
                    {
                        title: '修改备注',
                        id: 'SetName'
                    }, {
                        title: '删除好友',
                        id: 'del'
                    }
                ],
                click: function(res) {
                    if (res.id === 'info') {
                        var id = $(seft).attr("data-id");
                        parent.layer.open({
                            type: 2,
                            title: false,
                            content: 'page/chat_info.php?user_to=' + id,
                            area: ['336px', '450px'],
                            anim: 5,
                            maxmin: false,
                            shadeClose: true,
                            //scrollbar: false
                        });
                    }
                    if (res.id === 'del') {
                        var id = $(seft).attr("data-id");
                        DelUser(id, el);
                    }
                    if (res.id === 'SetName') {
                        var id = $(seft).attr("data-id");
                        SetName(id, el);
                    }
                },
                align: 'left',
                style: 'box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);'
            });
        });
        layui.dropdown.render({
            elem: '.header .state',
            trigger: 'hover',
            data: [{
                title: '<span class="state-1"></span>在线',
                id: 1
            }, {
                title: '<span class="state-2"></span>忙碌',
                id: 2
            }, {
                title: '<span class="state-3"></span>请勿打扰',
                id: 3
            }, {
                title: '<span class="state-0"></span>离线',
                id: 0
            }],
            click: function(obj) {
                setState(obj.id);
            }
        });
        new Sortable($(".layui-tab-item.user")[0], {
            handle: '.user-item',
            animation: 150,
            ghostClass: 'blue-background-class',
            onEnd: function(e) {
                var a = [];
                $('.layui-tab-item.user .user-item').each(function(index) {
                    var json = {
                        id: $(this).attr('data-id'),
                        indexs: index
                    };
                    a.push(json);
                });
                $.ajax({
                    url: api.url('SortUser'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        data: a
                    },
                    success: function(data) {
                        if (data.code != 1) {
                            layer.msg(data.msg, {
                                icon: data.code
                            });
                        }
                    },
                    error: r => layer.alert(r.responseText, {
                        icon: 2
                    })
                });

            }
        });
    }

    function setState(type) {
        $.ajax({
            url: api.url('setState'),
            type: 'POST',
            dataType: 'json',
            data: {
                type: type
            },
            beforeSend: function() {
                layer.msg("正在更新", {
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
                    $('.state').attr('class', 'state state-' + type);
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });

    }

    function QueryUser(tel, index) {
        $.ajax({
            url: api.url('QueryUser'),
            type: 'POST',
            dataType: 'json',
            data: {
                tel: tel
            },
            success: function(data) {
                if (data.code != 1) {
                    parent.layer.msg(data.msg, {
                        icon: data.code
                    });
                    return;
                }
                parent.layer.close(index);
                parent.layer.open({
                    type: 2,
                    title: false,
                    content: 'page/chat_info.php?user_to=' + data.data.id,
                    area: ['336px', '450px'],
                    anim: 5,
                    maxmin: false,
                    shadeClose: true,
                });
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }

    function DelUser(id, el) {
        $.ajax({
            url: api.url('DelUser'),
            type: 'POST',
            dataType: 'json',
            data: {
                user_to: id
            },
            beforeSend: function() {
                parent.layer.msg("正在删除", {
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
                    $(el).remove();
                    ChatMsg();
                    var elem = $('.user .user-item');
                    if (elem.length == 0) {
                        $('.user').html('<div class="not"></div>');
                    }
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }

    function SetName(id, el) {
        parent.layer.prompt({
            title: '修改备注名称',
            formType: 0,
            value: $(el).find('.user-name>span').eq(0).text()
        }, function(name, index) {
            $.ajax({
                url: api.url('SetName'),
                type: 'POST',
                dataType: 'json',
                data: {
                    user_to: id,
                    name: name
                },
                beforeSend: function() {
                    parent.layer.msg("正在修改", {
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
                        $(el).find('.user-name>span').eq(0).text(name);
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
            layer.close(index);

        });
    }

    init();
</script>

</html>