<?php
include "../php/api.php";
class _web extends _api
{
    public function _sendEmailCode()
    {
        //判断是否绑定过邮箱
        if ($this->user['email'] != '') {
            $this->res('您已经绑定过邮箱，请勿重复绑定', 3);
        }

        //判断是否启用邮箱服务
        if ($this->sys['mail_state'] == '0') {
            $this->res('邮箱服务不可用！', 3);
        }

        //验证最近发送的验证码时间

        $sql = "SELECT `found_date` as d FROM  `email_code` WHERE `ip` = '{$this->ip}' ORDER BY `id` DESC limit 1";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $d = strtotime($row['d']);
            $s = strtotime(date('Y-m-d H:i:s'));
            $c = intval($this->sys['sms_second']);
            if ($s - $d < $c) {
                $i = $c - ($s - $d);
                $this->res("请在<b style='color:red;'>{$i}</b>秒后操作", 3);
            }
        }
        $this->ajax(['email']);
        $email = $_REQUEST['email'];
        $code = rand(100000, 999999);
        require_once "../php/smtp.php";
        $s = new Smtp($this->sys['mail_server'], $this->sys['mail_port'], true, $this->sys['mail_user'], $this->sys['mail_secret']);
        $s->debug = false;
        $title = '尊敬的用户，' . $this->sys['title'] . '平台给您发了一封邮件';
        $content = "您正在进行邮箱绑定操作，验证码为：<b style='color:red;'>{$code}</b>，请勿将验证码泄露给他人。";
        $r = $s->sendmail($email, $this->sys['mail_account'], $title, $content, 'HTML');
        if (!$r) {
            $this->res('邮件发送失败，请联系管理员', 3);
        }
        $this->db->add('email_code', [
            'email' => $email,
            'code' => $code,
            'ip' => $this->ip
        ]);
        $this->res('邮件发送成功，请注意查收', 1);
    }

    public function _add()
    {
        $this->ajax(['email', 'code']);
        $email = $_REQUEST['email'];
        $code = $_REQUEST['code'];
        $this->_isEmailCode($email, $code);
        $sql = "UPDATE `user_data` SET `email` = '{$email}' WHERE `id` = '{$this->id}';";
        $this->run($sql, false, '邮箱绑定成功', '邮箱绑定失败');
    }

    //验证邮箱验证码
    public function _isEmailCode($email, $code)
    {
        $w = "WHERE `email` = '{$email}' ORDER BY `id` DESC limit 1";
        $sql = "SELECT `state`,`code` FROM  `email_code` {$w}";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $s = intval($row['state']);
            if ($s == 1) {
                $this->res('验证码不正确！', 3);
            }
            if ($code != $row['code']) {
                $this->res('验证码不正确！', 3);
            }
            $d = date('Y-m-d H:i:s');
            $sql = "UPDATE `email_code` SET `state` = '1',`veri_date` = '{$d}' {$w}";
            $res = $this->run($sql);
            return true;
        }
        $this->res('验证码不正确！', 3, '无发送记录');
    }
};
$web = new _web(2, 'id,email');
$web->method();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>绑定邮箱</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .mail-list {
            max-height: 200px;
            overflow: auto;
        }
    </style>
</head>

<body class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span>邮箱账号</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="email" class="layui-input" lay-verify="email" placeholder="请输入邮箱号" lay-type="email" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span>验证码</span>
        </label>
        <div class="layui-inline">
            <input type="text" name="code" class="layui-input" lay-verify="number" placeholder="请输入收到的验证码" />
        </div>
        <div class="layui-inline">
            <button class="layui-btn layui-btn-primary layui-border-blue layui-btn-sm getSmsCode">获取验证码</button>
        </div>
    </div>
    <div class="layui-footer layui-nobox">
        <button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">确认</button>
        <button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    var $ = layui.$,
        form = layui.form;
    form.on("submit(submit)", function(data) {
        $.ajax({
            url: api.url('add'),
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
                        var el = parent.$('.EmailBtn');
                        var email = $('input[name="email"]').val();
                        el.parent().find('.bd-list-item-text').html('已绑定邮箱：' + email);
                        el.parent().find('.bd-list-item-content').after('<a class="layui-table-link EmailRelieve">解除</a>');
                        el.remove();
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
    //获取验证码
    $(".getSmsCode").click(function() {
        getSmsCode($(this));
    });

    function getSmsCode(btn) {
        var elem = btn.parents(".layui-form").find("[name=email]"),
            email = elem.val();
        if (email == undefined || email == "") {
            parent.layer.msg('请填写邮箱号', {
                icon: 3
            });
            elem.focus();
            return false;
        }
        $.ajax({
            url: api.url('sendEmailCode'),
            type: "POST",
            dataType: "json",
            data: {
                email: email
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
                    var text = btn.text(),
                        time = Number("<?php echo $web->sys['sms_second']; ?>"),
                        x = 0;
                    btn.text(time + "秒后发送");
                    btn.unbind("click");
                    //在发送成功后60秒内禁止再次点击按钮进行发送
                    for (var i = time - 1; i >= -1; i--) {
                        (function(i) {
                            setTimeout(function() {
                                btn.text(i + "秒后发送");
                                if (i == -1) {
                                    btn.text(text);
                                    $(".getSmsCode").click(function() {
                                        getSmsCode($(this));
                                    });
                                }
                            }, (x + 1) * 1000);
                        })(i);
                        x++;
                    }
                }
            },
            error: function(data) {
                var obj = eval(data);
                layer.alert(obj.responseText, {
                    icon: 2
                });
            }
        });
    }
</script>

</html>