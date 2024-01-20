<?php
include '../php/api.php';
class _web extends _api
{
    //获取当前用户的聊天记录
    public function _UserMsg()
    {
        $data = [];
        $user_to = $this->is('user_to', false);
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $limit = isset($_REQUEST["limit"]) ? intval($_REQUEST["limit"]) : 10;
        $start = ($page - 1) * $limit;
        $f = "*,
        (SELECT `picture` FROM `user_data` WHERE user_data.id = chat_msg.user_id limit 1) AS `user_picture`,
        (SELECT `username` FROM `user_data` WHERE user_data.id = chat_msg.user_id limit 1) AS `user_username`,
        (SELECT `picture` FROM `user_data` WHERE user_data.id = chat_msg.user_to limit 1) AS `to_picture`,
        (SELECT `username` FROM `user_data` WHERE user_data.id = chat_msg.user_to limit 1) AS `to_username`,
        (SELECT `name` FROM `chat_user` WHERE chat_user.user_to = chat_msg.user_to limit 1) AS `name`";
        $sql = "SELECT {$f} FROM  `chat_msg` WHERE `user_id` = {$this->id} AND `user_to` = {$user_to} ORDER BY `id` DESC limit {$start},{$limit};";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        $sql = "UPDATE `user_message` SET `read` = 1 WHERE `user_to` = {$this->id} AND `type` = 1 AND `content` = '{$user_to}';";
        $this->run($sql);
        $sql = "UPDATE `chat_msg` SET `read` = 1 WHERE `user_id` = {$this->id} AND `user_to` = {$user_to} AND `read` = 0;";
        $this->run($sql);
        $this->res('调试成功', 1, $data);
    }

    //获取实时消息
    public function _getMsg()
    {
        $user_to = $this->is('user_to', false);
        $sql = "SELECT id FROM  `chat_msg` WHERE `user_id` = {$this->id}  AND `user_to` = {$user_to} ORDER BY `id` DESC LIMIT 1;";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $this->res('最新消息', 1, $row);
        }
        $this->res('暂无消息', 3);
    }

    //发送消息
    public function _send()
    {
        $this->ajax(['user_to', 'type', 'content']);
        $user_to = $_REQUEST['user_to'];
        $type = $_REQUEST['type'];
        $content = $_REQUEST['content'];
        $isUser = $this->_getUser($user_to);
        if (!$isUser) {
            $this->res('对方不是你的好友', 3);
        }
        $sql = "INSERT INTO `chat_msg` (`user_id`, `user_to`, `state`,`type`,`content`) VALUES ('{$this->id}', '{$user_to}', '0','{$type}','{$content}'),('{$user_to}', '{$this->id}', '1','{$type}','{$content}');";
        $this->sendMessage($user_to, 1, $this->user['picture'], "给你发送了一条消息", $this->id, 0);
        $this->run($sql);
        if ($user_to == '1' && $this->sys['robot_state'] == '1') {
            $sql = "SELECT `content` FROM  `chat_auto` WHERE `text` LIKE '%{$content}%';";
            $res = $this->run($sql);
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $content = $row['content'];
                    $sql = "INSERT INTO `chat_msg` (`user_id`, `user_to`, `state`,`type`,`content`) VALUES ('{$user_to}', '{$this->id}', '0','{$type}','{$content}'),('{$this->id}', '{$user_to}', '1','{$type}','{$content}');";
                    $this->run($sql);
                }
                $this->res('消息发送成功', 1);
            }
            $sql = "INSERT INTO `chat_msg` (`user_id`, `user_to`, `state`,`type`,`content`) VALUES ('{$user_to}', '{$this->id}', '0','{$type}','你的问题难到我了'),('{$this->id}', '{$user_to}', '1','{$type}','你的问题难到我了');";
            $this->run($sql, false, '消息发送成功', '消息发送失败');
        }
        $this->res('消息发送成功', 1);
    }

    //获取是否是好友关系
    public function _getUser($id)
    {
        $sql = "SELECT id FROM  `chat_user` WHERE `user_id` = {$this->id} AND `user_to` = {$id}";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            return true;
        }
        return false;
    }
}
$web = new _web(2, "id,picture");
$web->method();
$web->user_to = $web->is('user_to');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>聊天窗口</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            overflow: hidden;
            padding: 0;
        }

        .content {
            background-color: #F5F5F5;
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 150px;
            padding: 15px;
            overflow: auto;
        }

        .menu {
            position: absolute;
            left: 0;
            right: 0;
            height: 150px;
            border-top: 1px solid #f0f0f0;
            bottom: 0;
        }

        .msg-item {
            display: flex;
            margin-bottom: 20px;
        }

        .msg-picture {
            width: 60px;
            height: 60px;
            display: flex;
            cursor: pointer;
        }

        .msg-picture>img {
            width: 50px;
            height: 50px;
            margin: auto;
            border-radius: 100%;
        }

        .msg-info {
            width: calc(100% - 60px);
        }

        .msg-name {
            margin-top: 5px;
            font-size: 16px;
            padding-left: 10px;
        }

        .msg-body {
            padding-left: 10px;
            padding-top: 5px;
        }

        .msg-text {
            display: inline-block;
            background-color: #ffffff;
            color: #333333;
            padding: 8px 10px;
            border-radius: 5px;
            position: relative;
            border: 1px solid #e2e2e2;
            max-width: 80%;
            word-break: break-all;
            min-height: 10px;
            min-width: 10px;
        }

        .msg-text::before {
            content: '';
            width: 10px;
            height: 10px;
            background-color: #ffffff;
            position: absolute;
            border-left: 1px solid #e2e2e2;
            border-top: 1px solid #e2e2e2;
            transform: rotate(-45deg);
            left: -6px;
            top: 11px;
        }

        .msg-time {
            font-size: 12px;
            color: #b2b2b2;
            margin-bottom: 5px;
            padding-left: 5px;
        }

        .msg-item.this {
            flex-direction: row-reverse;
        }

        .msg-item.this .msg-info {
            text-align: right;
        }

        .msg-item.this .msg-text::before {
            transform: rotate(135deg);
            left: auto;
            right: -6px;
            top: 11px;
            background-color: #15C377;
        }

        .msg-item.this .msg-text {
            text-align: left;
            background-color: #15C377;
            color: #ffffff;
        }

        .tool {
            height: 45px;
            background-color: #f0f0f0;
            line-height: 45px;
            padding: 0px 15px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }

        .tool>i {
            margin: 0px 5px;
            cursor: pointer;
        }

        .tool>i:hover {
            color: #1E90FF;
        }

        .body {
            height: calc(100% - 45px);
            position: relative;
        }

        .editor {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            padding: 15px;
            outline: none;
            overflow: auto;
        }

        .btn-box {
            position: absolute;
            right: 15px;
            bottom: 15px;
        }

        .msg-text img,
        .editor img {
            max-width: 120px;
            cursor: pointer;
        }

        .emoticon {
            position: absolute;
            width: 350px;
            height: 250px;
            background-color: #ffffff;
            box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.2);
            border: 1px solid #e2e2e2;
            border-radius: 4px;
            left: 5px;
            bottom: 150px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            display: none;
        }

        .emoticon::before {
            content: '';
            width: 15px;
            height: 15px;
            background-color: #ffffff;
            position: absolute;
            border-left: 1px solid #e2e2e2;
            border-top: 1px solid #e2e2e2;
            transform: rotate(-135deg);
            left: 12px;
            bottom: -9px;
            box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.2);
        }

        .emoticon-box {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            right: 0;
            background-color: #ffffff;
            border-radius: 4px;
            overflow: hidden;
        }

        .emoticon-list {
            width: 100%;
            height: 209px;
            overflow: auto;
            position: relative;
        }

        .emoticon-nav {
            width: 100%;
            height: 40px;
            border-top: 1px solid #f0f0f0;
            display: flex;
        }

        .emoticon-nav>li {
            padding: 2px 10px;
            line-height: 40px;
            text-align: center;
            border-right: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .emoticon-nav>li:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .emoticon-item {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            padding: 10px;
            display: none;
            overflow: auto;
        }

        .emoticon-item.this {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .emoticon-item>li {
            height: 22px;
            width: 22px;
            margin: 5px;
            overflow: hidden;
            border-radius: 5px;
            text-align: center;
            font-size: 0;
            cursor: pointer;
        }

        .emoticon-item>li:hover {
            opacity: 0.7;
        }

        .emoji {
            width: 22px;
            height: 22px;
            background-image: url(../images/emoji.png);
            display: inline-block;
            background-size: 100% auto;
            vertical-align: middle;
        }
    </style>
</head>

<body class="layui-form">
    <div class="content layui-scrollbar">
        <!-- 聊天内容 -->
    </div>
    <div class="menu">
        <!-- 表情包 -->
        <div class="emoticon">
            <div class="emoticon-box">
                <div class="emoticon-list">
                    <div class="emoticon-item this emoji-box">
                    </div>
                    <div class="emoticon-item">我的收藏</div>
                </div>
                <div class="emoticon-nav">
                    <li>系统表情</li>
                    <li>我的收藏</li>
                </div>
            </div>
        </div>
        <div class="tool">
            <i class="layui-icon layui-icon-face-smile" title="添加表情"></i>
            <i class="layui-icon layui-icon-picture" title="上传图片"></i>
            <!-- <i class="layui-icon layui-icon-file-b" title="上传文件"></i> -->
        </div>
        <div class="body">
            <div class="editor layui-scrollbar" id="editor" contenteditable="true"></div>
            <div class="btn-box">
                <button type="button" class="layui-btn layui-btn-normal send">发送</button>
                <button type="button" class="layui-btn layui-btn-primary close">关闭</button>
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/editor.js?v=<?php echo $web->v; ?>"></script>
<script>
    var GetMsg = false;

    function nav() {
        var li = (".emoticon-nav>li"),
            de = $(".emoticon-list");
        $(document).on("click", li, function() {
            var index = $(li).index(this);
            $(li).siblings(".this").removeClass("this");
            $(this).addClass("this");
            de.children(".this").removeClass("this");
            de.children(".emoticon-item").eq(index).addClass("this");
        });
        $(li).eq(0).trigger("click");
    }


    function UserMsg(user_to) {
        $.ajax({
            url: api.url('UserMsg'),
            type: 'POST',
            dataType: 'json',
            data: {
                user_to: user_to,
                page: page,
                limit: 100
            },
            success: function(data) {

                if (data.code == 1) {
                    var elem = $(".content");
                    if (page == 1) {
                        elem.html('');
                    }
                    for (var i = data.data.length - 1; i >= 0; i--) {
                        var json = data.data[i];
                        var state = json.state == '1' ? '' : 'this';
                        var id = json.state == '1' ? json.user_to : json.user_id;
                        var picture = json.state == '1' ? json.to_picture : json.user_picture;
                        var username = json.state == '1' ? (json.name != '' ? json.name : json.to_username) : json.user_username;
                        var content = SetText(json.content);
                        var item = `<div class="msg-item ${state}">
    <div class="msg-picture" data-id="${id}">
        <img src="${picture != '' ? picture : '../images/picture.png'}" alt="">
    </div>
    <div class="msg-info">
        <div class="msg-name">${username}</div>
        <div class="msg-body">
            <div class="msg-time">${json.found_date}</div>
            <div class="msg-text">${content}</div>
        </div>
    </div>
</div>`;
                        var input = $('<input />');
                        elem.append(item).append(input);
                        input.focus();
                        input.remove();
                        if (i == 0 && page == 1) {
                            window.id = json.id;
                        }
                    }
                    var iframe = parent.$("iframe[src='page/message.php']");
                    if (iframe.length > 0) {
                        iframe[0].contentWindow.window.init();
                    }

                    var iframe = parent.$("iframe[src='page/chat_user.php']");
                    if (iframe.length > 0) {
                        iframe[0].contentWindow.window.ChatMsg();
                    }
                    $(".editor").focus();
                    //roll();
                    if (page != 1) {
                        //$('.content').scrollTop(0);
                    }
                } else {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                }
                GetMsg && clearInterval(GetMsg);
                GetMsg = setTimeout(function() {
                    getMsg(user_to);
                }, 2000);
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }

    window.roll = function() {
        $('.content').unbind('scroll');
        $('.content').scroll(function() {
            var t = $(this).scrollTop();
            if (t == 0) {
                $(this).unbind('scroll');
                page = page + 1;
                UserMsg(user_to);
            }
        });
    }

    function init() {
        window.editor = new _editor('#editor');
        window.user_to = '<?php echo $web->user_to; ?>';
        window.page = 1;
        window.id = 0;
        UserMsg(user_to);
        $(".send").click(function() {
            send();
        });
        $(".close").click(function() {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        });
        //输入框回车查询
        $(".editor").keydown(function(e) {
            if (e.keyCode == 13 && !e.ctrlKey) {
                e.preventDefault();
                send();
            }
            if (e.keyCode == 13 && e.ctrlKey) {
                window.editor.insertHTML('<br/>');
            }
            if (e.keyCode == 27) {
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
            }
        });
        $(document).on('click', '.msg-text img', function() {
            var url = $(this).attr('src');
            parent.App.scroll(url);
        });
        $(document).on('click', '.msg-picture', function() {
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

        $('.tool>.layui-icon-picture').click(function() {
            uploadImg(function(data) {
                parent.layer.msg(data.msg, {
                    icon: data.code
                });
                var img = '<img src="' + data.data.host + '"/>';
                window.editor.insertHTML(img);
            });
        });
        nav();
        emoji();

        $(document).on('click', '.emoji-box>li', function() {
            var el = $(this).html();
            window.editor.insertHTML(el);
            $(".emoticon").hide(0);
        });

        $('.tool>.layui-icon-face-smile').click(function() {
            $(".emoticon").show(0);
            setTimeout(function() {
                $(document).on('click.emoticon', function(e) {
                    var el = $(e.target).parents('.emoticon');
                    $(this).off('click.emoticon');
                    if (el.length == 0) {
                        $(".emoticon").hide(0);
                        return false;
                    }
                });
            }, 10);
        });
    }


    function emoji() {
        var h = 2750;
        var w = 22;
        var count = parseInt(h / w);
        var box = $('.emoji-box');
        box.html('');
        for (var i = 0; i < count; i++) {
            var t = i * w;
            switch (t) {
                case 2750:
                    t = t + 6;
                    break;
                case 2772:
                    t = t + 6;
                    break;
                case 2794:
                    t = t + 8;
                    break;
            }
            var item = `<li><i class ="emoji" contenteditable="false" style="background-position: 0px -${t}px;"></i></li>`;
            box.append(item);
        }
    }

    function uploadImg(s = false) {
        var file = $(`<input type="file" accept=".png,.jpg,.jpeg,.gif"/>`);
        file.click();
        file.change(function() {
            var f = $(this)[0].files,
                formData = new FormData();
            formData.append('file', f[0]);
            formData.append('path', 'upload/chat/');
            $.ajax({
                url: api.url('upload', '../?method='),
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    parent.layer.msg('<span class="layer-load">正在上传</span>', {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    });
                },
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        var Rate = ((e.loaded / e.total) * 100)
                            .toFixed(2) +
                            '%';
                        $(".layer-load").text("已上传" + Rate);
                    });
                    return xhr;
                },
                success: function(data) {
                    s && s(data);
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });
        });
    }

    function SetText(text) {
        var pattern = /((ht|f)tps?):\/\/[\w\-]+(\.[\w\-]+)+([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?/g;
        var arr = text.match(pattern);
        for (var key in arr) {
            var url = arr[key];
            var k = "page/url.php?url=";
            if (url.indexOf(k) != -1) {
                var str = window.atob(url.split(k)[1]);
                text = text.replace(url, '<a href="' + url + '" target="_blank" class="layui-table-link">' + str + '</a>');
            }
        }
        return text;
    }

    function getMsg(user_to) {
        $.ajax({
            url: api.url('getMsg'),
            type: 'POST',
            dataType: 'json',
            data: {
                user_to: user_to
            },
            success: function(data) {
                if (data.code == 1) {
                    if (data.data.id != window.id) {
                        UserMsg(window.user_to);
                    } else {
                        GetMsg && clearInterval(GetMsg);
                        GetMsg = setTimeout(function() {
                            getMsg(user_to);
                        }, 2000)
                    }
                } else {
                    GetMsg && clearInterval(GetMsg);
                    GetMsg = setTimeout(function() {
                        getMsg(user_to);
                    }, 2000);
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });

    }

    function play(f, s) {
        var elem = $('<audio src="../mp3/' + f + '.mp3" autoplay="autoplay"></audio>');
        $("body").append(elem);
        elem.on("ended", function() {
            $(this).remove();
            s && s();
        });
    }

    function send() {
        var conetnt = $(".editor").html();
        if (conetnt == '') {
            parent.layer.msg('请输入内容', {
                icon: 3
            });
            return false;
        }
        $.ajax({
            url: api.url('send'),
            type: 'POST',
            dataType: 'json',
            data: {
                user_to: window.user_to,
                type: 'text',
                content: conetnt
            },
            beforeSend: function() {
                parent.layer.msg("正在发送", {
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
                    UserMsg(window.user_to);
                    $(".editor").html('').focus();
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });

    }
    init();
</script>

</html>