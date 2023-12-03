<?php
include '../php/api.php';
class _web extends _api
{
    //修改网页布局
    public function _set()
    {
        $t = $this->is('type');
        $v = $this->is('value');
        $a = ['theme', 'show_footer', 'show_msg', 'special_color', 'tab_type', 'select_type', 'anim_state', 'time_state', 'show_chat', 'is_mobile'];
        if (!in_array($t, $a)) {
            $this->res('方法不存在', 3);
        }
        $s = "`{$t}` = '{$v}'";
        $sql = "UPDATE `user_data` SET {$s} WHERE `id` = {$this->id};";
        $this->run($sql, false);
    }
}
$web = new _web(2, "*");
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>界面设置</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .layui-card-header {
            color: #999999;
        }

        .layui-form-item .layui-form-label {
            width: 100px;
            text-align: left;
        }

        .layui-form-item {
            padding-left: 20px;
        }

        .theme-btn {
            display: inline-block;
            vertical-align: top;
            width: 20px;
            height: 20px;
            background-color: #0044FF;
            margin: 0px 5px;
            border: none;
            border-radius: 2px;
            cursor: pointer;
        }

        .theme {
            text-align: center;
            display: grid;
            grid-gap: 10px;
            grid-template-columns: 25px 25px 25px 25px 25px 25px 25px 25px;
            grid-template-rows: 25px;
            padding-left: 35px;
        }

        .theme-item {
            background-color: #FFFFFF;
            cursor: pointer;
            position: relative;
            border-radius: 2px;
            box-shadow: 1px 1px 2px 0px #CCCCCC;
            overflow: hidden;
        }

        .theme-item:hover {
            box-shadow: 2px 2px 2px 0px #CCCCCC;
        }

        .theme-header {
            height: 10px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #f6f6f6;
        }

        .theme-nav {
            width: 15px;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            background-color: #191A23;
        }

        .theme-item.this::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            border-radius: 100%;
            background-image: url(../images/theme-yes.png);
            background-size: 100% 100%;
        }

        .layui-input-inline {
            text-align: right;
            padding-right: 30px;
        }

        .layui-form>.layui-card {
            box-shadow: none;
        }

        .layui-unselect.layui-form-select {
            width: 90px;
            float: right;
            text-align: left;
        }

        .layui-input.layui-unselect {
            height: 28px;
            width: 90px !important;
        }

        .layui-form-select dl dd.layui-this {
            background-color: transparent;
            color: #1E90FF;
        }

        .tips {
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>

<body class="layui-form">
    <div class="layui-card">
        <div class="layui-card-header">主题方案</div>
        <div class="layui-card-body">
            <div class="theme">
                <div class="theme-item" theme="1E90FF" title="漆黑">
                    <div class="theme-header"></div>
                    <div class="theme-nav"></div>
                </div>
                <div class="theme-item" theme="33CABB" title="湖绿">
                    <div class="theme-header" style="background-color: #FFFFFF;"></div>
                    <div class="theme-nav" style="background-color: #33CABB;"></div>
                </div>
                <div class="theme-item" theme="222437" title="黛蓝">
                    <div class="theme-header" style="background-color: #FFFFFF;"></div>
                    <div class="theme-nav" style="background-color: #222437;"></div>
                </div>
                <div class="theme-item" theme="009688" title="青碧">
                    <div class="theme-header" style="background-color: #FFFFFF;"></div>
                    <div class="theme-nav" style="background-color: #009688;"></div>
                </div>
                <div class="theme-item" theme="1e9fff" title="靛青">
                    <div class="theme-header" style="background-color: #FFFFFF;"></div>
                    <div class="theme-nav" style="background-color: #1e9fff;"></div>
                </div>
                <div class="theme-item" theme="ffb800" title="橙黄">
                    <div class="theme-header" style="background-color: #FFFFFF;"></div>
                    <div class="theme-nav" style="background-color: #ffb800;"></div>
                </div>
                <div class="theme-item" theme="673ab7" title="青莲">
                    <div class="theme-header" style="background-color: #FFFFFF;"></div>
                    <div class="theme-nav" style="background-color: #673ab7;"></div>
                </div>
                <div class="theme-item" theme="FFFFFF" title="白色">
                    <div class="theme-header" style="background-color: #FFFFFF;"></div>
                    <div class="theme-nav" style="background-color: #FFFFFF;"></div>
                </div>
            </div>
        </div>
        <div class="layui-card-header">样式设置</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label">配色选择</label>
                <div class="layui-input-inline">
                    <select name="special_color" lay-verify="required" lay-filter="special_color">
                        <option value="">默认</option>
                        <option value="filter: brightness(80%);">夜间</option>
                        <option value="filter: grayscale();">灰色</option>
                        <option value="filter: blur(2px);">模糊</option>
                        <option value="filter: invert(100%);">反转</option>
                        <option value="filter: sepia(100%);">褐色</option>
                        <option value="filter: hue-rotate(60deg);">紫色</option>
                        <option value="filter: hue-rotate(120deg);">品红</option>
                        <option value="filter: hue-rotate(180deg);">橘红</option>
                        <option value="filter: hue-rotate(240deg);">葱青</option>
                        <option value="filter: hue-rotate(300deg);">油绿</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span>自动配色</span>
                    <i class="layui-icon layui-icon-about" lay-tips="根据节假日或者纪念日自动设置配色"></i>
                </label>
                <div class="layui-input-inline">
                    <input type="checkbox" name="time_state" lay-skin="switch" lay-filter="time_state" />
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">页签风格</label>
                <div class="layui-input-inline">
                    <select name="tab_type" lay-verify="required" lay-filter="tab_type">
                        <option value="2">卡片</option>
                        <option value="3">文本</option>
                        <option value="0">圆点</option>
                        <option value="1">矩形</option>
                        <option value="4">按钮</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">导航菜单</label>
                <div class="layui-input-inline">
                    <select name="select_type" lay-verify="required" lay-filter="select_type">
                        <option value="0">细三角</option>
                        <option value="1">粗三角</option>
                        <option value="2">加减号</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-card-header">布局设置</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span>显示通知</span>
                </label>
                <div class="layui-input-inline">
                    <input type="checkbox" name="show_msg" lay-skin="switch" lay-filter="show_msg" />
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span>显示聊天</span>
                </label>
                <div class="layui-input-inline">
                    <input type="checkbox" name="show_chat" lay-skin="switch" lay-filter="show_chat" />
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span>显示页脚</span>
                </label>
                <div class="layui-input-inline">
                    <input type="checkbox" name="show_footer" lay-skin="switch" lay-filter="show_footer" />
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span>适应手机</span>
                    <i class="layui-icon layui-icon-about" lay-tips="根据不同终端自适应布局"></i>
                </label>
                <div class="layui-input-inline">
                    <input type="checkbox" name="is_mobile" lay-skin="switch" lay-filter="is_mobile" />
                </div>
            </div>
        </div>
        <div class="layui-card-header">其他设置</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span>路由动画</span>
                </label>
                <div class="layui-input-inline">
                    <input type="checkbox" name="anim_state" lay-skin="switch" lay-filter="anim_state" />
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span>注销登录</span>
                </label>
                <div class="layui-input-inline">
                    <button class="layui-btn layui-btn-plug-danger layui-btn-sm quit">
                        <i class="layui-icon layui-icon-logout"></i>
                        <span>退出账号</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="tips">
            <p>网站由<?php echo $web->server; ?>提供技术支持</p>
        </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    $(document).on("click", ".theme-item", function() {
        var theme = $(this).attr("theme");
        $('.theme-item').removeClass("this");
        $(this).addClass("this");
        parent.$("body").attr("theme", theme);
        setTheme('theme', theme);
    });
    form.on('switch(show_footer)', function(data) {
        setTheme('show_footer', data.elem.checked ? 1 : 0);
        parent.$("body").attr('show-footer', data.elem.checked ? 1 : 0);
    });
    form.on('switch(show_msg)', function(data) {
        setTheme('show_msg', data.elem.checked ? 1 : 0);
        parent.$(".message").attr('class', data.elem.checked ? 'message' : 'message layui-hide');
        if (data.elem.checked) {
            parent.App.message();
        }
    });
    form.on('switch(anim_state)', function(data) {
        setTheme('anim_state', data.elem.checked ? 1 : 0);
        parent.window.anim = data.elem.checked ? '1' : '0';
        parent.$(".layui-tab-item>iframe").attr('class', data.elem.checked ? 'layui-anim layui-anim-up' : '');
    });
    form.on('switch(time_state)', function(data) {
        setTheme('time_state', data.elem.checked ? 1 : 0, function() {
            parent.window.time_state = data.elem.checked ? '1' : '0';
            parent.location.reload();
        });
    });

    form.on('switch(show_chat)', function(data) {
        setTheme('show_chat', data.elem.checked ? 1 : 0, function() {
            parent.window.time_state = data.elem.checked ? '1' : '0';
            parent.location.reload();
        });
    });
    form.on('select(special_color)', function(data) {
        setTheme('special_color', data.value);
        var t = $("[name=time_state]").prop('checked');
        if (!t) {
            parent.$("html").attr('style', data.value);
        }
    });
    form.on('switch(is_mobile)', function(data) {
        setTheme('is_mobile', data.elem.checked ? 1 : 0, function() {
            parent.location.reload();
        });
    });
    form.on('select(tab_type)', function(data) {
        setTheme('tab_type', data.value);
        parent.$("body").attr('tab-type', data.value);
    });

    form.on('select(select_type)', function(data) {
        setTheme('select_type', data.value);
        parent.$("body").attr('select-type', data.value);
    });

    $('.quit').click(function(e) {
        e.preventDefault();
        parent.layer.confirm('确定要退出当前登录账号吗？', (index) => {
            layer.close(index);
            $.ajax({
                url: '../?method=quit',
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    if (res.code == 1) {
                        parent.layer.msg(res.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            parent.location.reload();
                        });
                    } else {
                        layer.msg(res.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                }
            });
        });
    });

    function setTheme(type, value, f = false) {
        $.ajax({
            url: api.url('set'),
            type: 'POST',
            dataType: 'json',
            data: {
                type: type,
                value: value
            },
            beforeSend: function() {
                layer.msg("正在同步", {
                    icon: 16,
                    shade: 0.05,
                    time: false
                });
            },
            success: function(data) {
                layer.msg(data.msg, {
                    icon: data.code
                }, function() {
                    f && f(data);
                });
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });

    }

    function init() {
        $(".theme-item").each(function() {
            var theme = parent.$("body").attr("theme");
            var value = $(this).attr("theme");
            if (theme == value) {
                $(this).addClass("this");
            }
        });
        $("[name=show_footer]").prop('checked', parent.$("body").attr('show-footer') == '1' ? true : false);
        $("[name=show_msg]").prop('checked', parent.$(".message").attr('class') == 'message' ? true : false);
        $("[name=anim_state]").prop('checked', parent.window.anim == '1' ? true : false);
        $("[name=time_state]").prop('checked', parent.window.time_state == '1' ? true : false);
        $("[name=show_chat]").prop('checked', parent.window.chat == '1' ? true : false);
        $("[name=is_mobile]").prop('checked', parent.$('meta[name=viewport]').length > 0 ? true : false);
        $("[name=special_color]").val(parent.$("html").attr('style'));
        $("[name=tab_type]").val(parent.$("body").attr('tab-type'));
        $("[name=select_type]").val(parent.$("body").attr('select-type'));
        form.render();
    }

    init();
</script>

</html>