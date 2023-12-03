<?php
include '../php/api.php';
class _web extends _api
{

    //获取标签
    public function _tags()
    {
        $html = '';
        $sql = "SELECT `name` FROM  `user_tags` WHERE `user_id` = {$this->id};";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $name = $row['name'];
                $html .= "<span class='layui-badge layui-bg-gray'><i class='layui-icon layui-icon-close'></i>{$name}</span>\n";
            }
        }
        return $html;
    }

    //更新资料
    public function _set()
    {
        $q = $this->getSql('user_data', ['nickname', 'explain', 'street', 'area', 'tel:tel'], "`id` = {$this->id}");
        $sql = $q['upd'];
        $this->run($sql, false);
    }

    //修改头像
    public function _setPicture()
    {
        $q = $this->getSql('user_data', ['picture'], "`id` = {$this->id}");
        $sql = $q['upd'];
        $this->run($sql, false);
    }

    //修改签名
    public function _SetMood()
    {
        $this->ajax(['value']);
        $v = $_REQUEST['value'];
        $sql = "UPDATE `user_data` SET `mood` = '{$v}' WHERE `id` = {$this->id};";
        $this->run($sql, false);
    }

    //添加标签
    public function _AddTags()
    {
        $this->ajax(['name']);
        $n = $_REQUEST['name'];
        $time = time();
        $this->db->add('user_tags', [
            'user_id' => $this->id,
            'name' => $n,
            'found_date' => $time
        ]);
        $this->res('添加标签成功', 1);
    }

    //删除标签
    public function _DelTags()
    {
        $this->ajax(['name']);
        $n = $_REQUEST['name'];
        $q = "DELETE FROM `user_tags` WHERE `name` = '{$n}' AND `user_id` = {$this->id};";
        $this->run($q, false);
    }

    public function _EmailRelieve()
    {
        //解除邮箱绑定
        $sql = "UPDATE `user_data` SET `email` = '' WHERE `id` = '{$this->id}' limit 1;";
        $this->run($sql, false, '邮箱解除绑定成功', '邮箱解除绑定失败');
    }
}
$web = new _web(2, "*,(SELECT `name` FROM `roles_list` WHERE roles_list.id = user_data.roles_id) as roles_name");
$web->method();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>账号设置</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .layui-text p {
            margin: 6px 0px;
        }

        .text-center {
            text-align: center;
        }

        .picture {
            width: 110px;
            height: 110px;
            position: relative;
            display: inline-block;
            border-radius: 50%;
            border: 2px solid #eee;
        }

        .picture::before {
            display: none;
        }

        .upload-item:hover {
            border: 2px solid #eee;
        }

        .picture:hover::after {
            content: '\e65d';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            color: #eee;
            width: auto;
            height: auto;
            background: rgba(0, 0, 0, 0.5);
            font-family: layui-icon;
            font-size: 24px;
            font-style: normal;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            cursor: pointer;
            line-height: 110px;
            border-radius: 50%;
            z-index: 999;
            border: none;
        }

        .user-info-head img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
        }

        .info-list-item {
            position: relative;
            padding-bottom: 8px;
        }

        .info-list-item>.layui-icon {
            position: absolute;
        }

        .info-list-item>p {
            padding-left: 30px;
        }

        .dash {
            border-bottom: 1px dashed #ccc;
            margin: 15px 0;
        }

        .bd-list-item {
            padding: 14px 0;
            border-bottom: 1px solid #e8e8e8;
            position: relative;
        }

        .bd-list-item .bd-list-item-img {
            width: 48px;
            height: 48px;
            line-height: 48px;
            margin-right: 12px;
            display: inline-block;
            vertical-align: middle;
        }

        .bd-list-item .bd-list-item-content {
            display: inline-block;
            vertical-align: middle;
        }

        .bd-list-item .bd-list-item-lable {
            margin-bottom: 4px;
            color: #333;
        }

        .layui-table-link {
            position: absolute;
            right: 0;
            top: 50%;
            text-decoration: none !important;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .editMood {
            margin-top: 8px;
            height: 22px;
        }

        .editTags {
            height: 22px;
            font-size: 12px;
        }

        .layui-badge-list span .layui-icon {
            right: 2px;
            top: 4px;
            z-index: 9999;
            color: red;
            width: 13px;
            height: 13px;
            line-height: 13px;
            border-radius: 50%;
            font-size: 10px;
            position: absolute;
            display: none;
        }

        .layui-badge-list span:hover .layui-icon {
            display: block;
            background-color: #FF5722;
            color: #fff;
        }

        .layui-badge-list .layui-badge {
            padding: 0px 7px;
            border: 1px solid #ccc;
            margin-bottom: 8px;
            background-color: #fafafa !important;
        }

        .layui-tab-item {
            padding-top: 20px;
        }

        .layui-form-item {
            margin-bottom: 25px;
        }

        .layui-table-link.EmailRelieve,
        .layui-table-link.WechatRelieve {
            color: red !important;
        }
    </style>
</head>

<body class="layui-form">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-msg">
                        <i class="layui-icon layui-icon-tips"></i>
                        <p>温馨提示：您可以在这里修改您的个人信息，以及绑定您的邮箱和微信。</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- 左侧 -->
        <div class="layui-col-sm12 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 25px;">
                    <div class="text-center layui-text">
                        <div class="upload-item picture" path="upload/picture/" cut>
                            <img src="<?php echo $web->user['picture']; ?>" />
                        </div>
                        <h2 style="padding-top: 20px;">
                            <?php echo $web->user['username']; ?>
                        </h2>
                        <p style="padding-top: 8px;" class="userMood"><?php echo $web->user['mood']; ?></p>
                    </div>
                    <div class="layui-text" style="padding-top: 30px;">
                        <div class="info-list-item">
                            <i class="layui-icon layui-icon-username"></i>
                            <p>UID：<?php echo $web->id; ?></p>
                        </div>
                        <div class="info-list-item">
                            <i class="layui-icon layui-icon-rmb"></i>
                            <p>余额：<?php echo $web->user['moneys']; ?>元</p>
                        </div>
                        <div class="info-list-item">
                            <i class="layui-icon layui-icon-template-1"></i>
                            <p>角色：<?php echo $web->user['roles_name'] == '' ? '默认' : $web->user['roles_name']; ?></p>
                        </div>
                        <div class="info-list-item">
                            <i class="layui-icon layui-icon-time"></i>
                            <p>注册时间：<?php echo $web->user['found_date']; ?></p>
                        </div>
                    </div>
                    <div class="dash"></div>
                    <h3>标签 <i class="layui-tags layui-icon layui-icon-add-1" style="color: #666"></i> </h3>
                    <div class="layui-badge-list" style="padding-top: 6px;">
                        <?php echo $web->_tags(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- 右 -->
        <div class="layui-col-sm12 layui-col-md9">
            <div class="layui-card">
                <div class="layui-card-body">

                    <div class="layui-tab layui-tab-brief" lay-filter="userInfoTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this">基本信息</li>
                            <li>账号绑定</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <div class="layui-row layui-col-space15">
                                    <div class="layui-col-md4">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">昵称:</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="nickname" value="<?php echo isset($web->user['nickname']) ? $web->user['nickname'] : ''; ?>" class="layui-input" />
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">个人简介:</label>
                                            <div class="layui-input-block">
                                                <textarea name="explain" class="layui-textarea"><?php echo isset($web->user['explain']) ? $web->user['explain'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">街道地址:</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="street" value="<?php echo isset($web->user['street']) ? $web->user['street'] : ''; ?>" class="layui-input" />
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">联系电话:</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="area" value="<?php echo isset($web->user['area']) ? $web->user['area'] : ''; ?>" style="width: 60px;" class="layui-input" />
                                                <div style="position: absolute;left: 65px;right: 0;top: 0;">
                                                    <input type="text" name="tel" value="<?php echo isset($web->user['tel']) ? $web->user['tel'] : ''; ?>" class="layui-input" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-normal" lay-filter="submit" lay-submit>更新基本信息</button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item" style="padding: 6px 25px 30px 25px;">
                                <div class="bd-list layui-text">
                                    <div class="bd-list-item">
                                        <div class="bd-list-item-content">
                                            <div class="bd-list-item-lable">
                                                <i class="layui-icon layui-icon-cellphone"></i>
                                                <span>手机账号</span>
                                            </div>
                                            <div class="bd-list-item-text">已绑定手机：<?php echo $web->user['username']; ?></div>
                                        </div>
                                    </div>
                                    <div class="bd-list-item">
                                        <div class="bd-list-item-content">
                                            <div class="bd-list-item-lable">
                                                <i class="layui-icon layui-icon-email"></i>
                                                <span>邮箱账号</span>
                                            </div>
                                            <div class="bd-list-item-text">已绑定邮箱：<?php echo $web->user['email'] != '' ? $web->user['email'] : '未绑定'; ?></div>
                                        </div>
                                        <?php echo $web->user['email'] == '' ? '<a class="layui-table-link EmailBtn">绑定</a>' : '<a class="layui-table-link EmailRelieve">解除</a>'; ?>
                                    </div>
                                    <div class="bd-list-item">
                                        <div class="bd-list-item-img">
                                            <i class="layui-icon layui-icon-login-qq" style="color: #3492ED;font-size: 48px;"></i>
                                        </div>
                                        <div class="bd-list-item-content">
                                            <div class="bd-list-item-lable">绑定QQ</div>
                                            <div class="bd-list-item-text">当前未绑定QQ账号</div>
                                        </div>
                                        <a class="layui-table-link layui-hide">绑定</a>
                                    </div>
                                    <div class="bd-list-item">
                                        <div class="bd-list-item-img">
                                            <i class="layui-icon layui-icon-login-wechat" style="color: #4DAF29;font-size: 48px;"></i>
                                        </div>
                                        <div class="bd-list-item-content">
                                            <div class="bd-list-item-lable">绑定微信</div>
                                            <div class="bd-list-item-text">当前未绑定绑定微信账号</div>
                                        </div>
                                        <a class="layui-table-link layui-hide">绑定</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    form.on("submit(submit)", function(data) {
        $.ajax({
            url: api.url('set'),
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
                layer.msg(data.msg, {
                    icon: data.code
                });
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
        return false;
    });

    $('.userMood').dblclick(function() {
        var el = $(this),
            html = el.text();
        el.hide();
        $(el).parent().append('<input class="editMood layui-input" type="text" maxlength="12" value="' + html + '">');
    });

    $('.layui-card-body').on('blur', '.editMood', function() {
        var el = $(this),
            html = el.val();
        el.remove();
        $('.userMood').text(html);
        $('.userMood').show();
        $.post(api.url('SetMood'), {
            value: html
        }, function(res) {})
    })

    $('.layui-tags').click(function() {
        if ($('.editTags').length <= 0) {
            $(this).parent().append('<input class="editTags layui-input" type="text" maxlength="10">');
        }
    })

    // 添加标签
    $('.layui-card-body').on('blur', '.editTags', function() {
        var that = $(this),
            html = that.val();
        that.remove();
        if (html == '') {
            return;
        }
        $.ajax({
            url: api.url('AddTags'),
            type: 'POST',
            dataType: 'json',
            data: {
                name: html
            },
            beforeSend: function() {
                layer.msg("正在添加", {
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
                    var elem = '\n';
                    elem += '<span class="layui-badge layui-bg-gray">';
                    elem += '<i class="layui-icon layui-icon-close"></i>';
                    elem += html;
                    elem += '</span>';
                    $('.layui-badge-list').append(elem);
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    })

    // 删除标签
    $('.layui-card-body').on('click', '.layui-badge-list i', function() {
        var that = $(this),
            html = that.parent('span').text();
        $.ajax({
            url: api.url('DelTags'),
            type: 'POST',
            dataType: 'json',
            data: {
                name: html
            },
            beforeSend: function() {
                layer.msg("正在删除", {
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
                    that.parent('span').remove();
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    })

    api.success = function(el, data) {
        var c = el.attr('class');
        if (c == 'upload-item picture') {
            $.ajax({
                url: api.url('setPicture'),
                type: 'POST',
                dataType: 'json',
                data: {
                    picture: data.data.host
                },
                beforeSend: function() {
                    layer.msg("正在加载", {
                        icon: 16,
                        shade: 0.05,
                        time: false
                    });
                },
                success: function(res) {
                    alert();
                    layer.msg(res.msg, {
                        icon: res.code
                    });
                    if (res.code == 1) {
                        el.find('img').attr('src', data.data.host);
                    }
                },
                error: r => layer.alert(r.responseText, {
                    icon: 2
                })
            });

        }
    }

    api.cut = (el, data) => {
        $.ajax({
            url: api.url('setPicture'),
            type: 'POST',
            dataType: 'json',
            data: {
                picture: data.data.host
            },
            beforeSend: function() {
                layer.msg("正在加载", {
                    icon: 16,
                    shade: 0.05,
                    time: false
                });
            },
            success: function(res) {
                layer.msg(res.msg, {
                    icon: res.code
                });
                if (res.code == 1) {
                    el.find('img').attr('src', data.data.host);
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    };
    //绑定邮箱
    $(document).on('click', '.EmailBtn', function() {
        layer.open({
            type: 2,
            title: '绑定邮箱',
            area: ["550px", "350px"],
            maxmin: false,
            content: "binding_email.php",
            shade: 0.3
        });
    });

    //解除邮箱
    $(document).on('click', '.EmailRelieve', function() {
        var self = $(this);
        layer.confirm('确定解除绑定邮箱吗？', function(index) {
            $.ajax({
                url: api.url('EmailRelieve'),
                type: "POST",
                dataType: "json",
                beforeSend: function() {
                    layer.msg("正在解除绑定", {
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
                        layer.close(index);
                        self.parent().find('.bd-list-item-text').html('已绑定邮箱：未绑定');
                        self.parent().find('.bd-list-item-content').after('<a class="layui-table-link EmailBtn">绑定</a>');
                        self.remove();
                    }
                },
                error: function(data) {
                    var obj = eval(data);
                    layer.alert(obj.responseText, {
                        icon: 2
                    });
                }
            });
        });
    });
</script>

</html>