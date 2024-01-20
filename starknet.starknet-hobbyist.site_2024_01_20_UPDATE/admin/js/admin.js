var w = {
    init() {
        this.i(),
            this.a()
    },
    a() {
        $.ajax({
            url: api.url("admin", "?method="),
            type: "POST",
            dataType: "json",
            success: function (e) {
                if (1 != e.code)
                    return layer.msg(data.msg, {
                        icon: data.code
                    });
                if ($(".layui-nav-img").attr("src", e.data.picture || "images/picture.png"),
                    $(".username").text(e.data.username),
                    "message" == $(".message").attr("class") && App.message(),
                    e.data.msg > 0 && $(".message-dot").removeClass("layui-hide").text(e.data.msg),
                    "1" == window.chat && layer.open({
                        type: 2,
                        title: "在线聊天",
                        content: "page/chat_user.php",
                        area: ["320px", "650px"],
                        offset: "rb",
                        anim: 5,
                        maxmin: !0,
                        shade: 0,
                        shadeClose: !0
                    }),
                    0 == e.data.tab.length)
                    return App.isTab = !0,
                        App.init(0),
                        !0;
                for (var t in e.data.tab) {
                    var a = e.data.tab[t].url
                        , i = e.data.tab[t].title
                        , n = e.data.tab[t].nav;
                    App.add(a, i, n)
                }
                $(".layui-tab-title>li[lay-id='" + e.data.tab_url + "']").trigger("click"),
                    App.isTab = !0
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    },
    i() {
        window.onload = (() => {
            $(".load").fadeOut(100, function () {
                $(this).remove()
            })
        }
        ),
            $(".layui-side dd>a").click(function (e) {
                e.preventDefault(),
                    App.add(this)
            }),
            $(".page-right").click(() => $(".layui-tab-title").stop().animate({
                scrollLeft: "+=200px"
            }, 500)),
            $(".page-left").click(() => $(".layui-tab-title").stop().animate({
                scrollLeft: "-=200px"
            }, 500)),
            $(document).on("click", ".add-menu", () => App.editMenu()),
            $(document).on("click", ".add-item", function () {
                var e = $(this).parents("li").attr("data-id");
                App.editItem("", e)
            }),
            $(".refresh-this").click(function () {
                var e = $(".layui-tab-title>li.layui-this").attr("lay-id");
                $("iframe[src='" + e + "']")[0].contentWindow.location.reload(),
                    $(this).addClass("this"),
                    App.load()
            }),
            $(document).on("click", ".shade", () => $("body").removeClass("this")),
            $(document).on("click", "body.this .layui-side dd>a", () => $("body").removeClass("this")),
            $(".quit").click(() => layer.confirm("确定要退出当前账号吗？", () => App.quit())),
            $(".fullscreen").click(function () {
                var e = $(this).children("i")
                    , t = "layui-icon layui-icon-screen-"
                    , a = t + "full";
                if (e.attr("class") == a)
                    return App.FullScreen(),
                        void e.attr("class", "layui-icon layui-icon-screen-restore").parent().attr("title", "退出全屏");
                App.ExitScreen(),
                    e.attr("class", a).parent().attr("title", "全屏显示")
            }),
            $(".message").click(() => {
                var e = $(document).width() - 350 - 100 + "px";
                layer.open({
                    type: 2,
                    title: !1,
                    content: "page/message.php",
                    area: ["350px", "400px"],
                    offset: ["51px", e],
                    anim: 5,
                    closeBtn: 0,
                    shadeClose: !0,
                    scrollbar: !1
                })
            }
            ),
            $(".set_password").click(() => layer.open({
                type: 2,
                title: "修改密码",
                area: ["500px", "320px"],
                maxmin: !1,
                content: "page/set_pswd.php",
                shade: .3
            })),
            $(".set_info").click(function (e) {
                e.preventDefault(),
                    App.add(this)
            }),
            $(window).on("beforeunload", function () {
                $.ajax({
                    url: api.url("leave", "?method="),
                    type: "POST",
                    dataType: "json",
                    success: e => {
                        1 == e.code && console.log(e)
                    }
                    ,
                    error: e => layer.alert(e.responseText, {
                        icon: 2
                    })
                })
            }),
            util.event("lay-header-event", {
                menuLeft: function () {
                    var e = $("body")
                        , t = e.attr("class")
                        , a = $(this);
                    "this" == t ? (e.removeClass("this"),
                        a.attr("class", "layui-icon layui-icon-shrink-right")) : (e.addClass("this"),
                            a.attr("class", "layui-icon layui-icon-spread-left"))
                },
                menuRight: () => {
                    layer.open({
                        type: 2,
                        title: "界面设置",
                        content: "page/setup.php",
                        area: ["350px", "100%"],
                        offset: "rt",
                        anim: 5,
                        shadeClose: !0
                    })
                }
            }),
            $(".page-batch").click(function () {
                dropdown.render({
                    elem: this,
                    show: !0,
                    trigger: "click",
                    data: [{
                        title: "关闭当前标签",
                        id: "this"
                    }, {
                        title: "关闭其他标签",
                        id: "other"
                    }, {
                        title: "关闭所有标签",
                        id: "all"
                    }],
                    click: e => {
                        var t = "";
                        "this" === e.id && (t = $(".layui-tab-title>li.layui-this")),
                            "other" === e.id && (t = $(".layui-tab-title>li").not(".layui-this")),
                            "all" === e.id && (t = $(".layui-tab-title>li")),
                            t.children("i").trigger("click")
                    }
                    ,
                    align: "right",
                    style: "box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);"
                })
            }),
            api.menu(".layui-tab-title>li", function (e) {
                dropdown.render({
                    elem: e,
                    show: !0,
                    trigger: "contextmenu",
                    data: [{
                        title: "单独打开",
                        id: "new"
                    }, {
                        title: "刷新当前",
                        id: "refresh"
                    }, {
                        title: "关闭当前",
                        id: "this"
                    }, {
                        title: "关闭其他",
                        id: "other"
                    }, {
                        title: "关闭所有",
                        id: "all"
                    }],
                    click: t => {
                        var a;
                        if ("new" === t.id)
                            return window.open($(e).attr("lay-id"));
                        if ("refresh" == t.id) {
                            var i = $(e).attr("lay-id");
                            return $("iframe[src='" + i + "']")[0].contentWindow.location.reload(),
                                App.load(),
                                !1
                        }
                        "this" === t.id && (a = $(e)),
                            "other" === t.id && (a = $(".layui-tab-title>li").not(e)),
                            "all" === t.id && (a = $(".layui-tab-title>li")),
                            a.children("i").trigger("click")
                    }
                    ,
                    align: "right",
                    style: "box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);"
                })
            });
        $(document).on("dblclick", ".layui-layout>.layui-body>.layui-tab>.layui-tab-title>li", function () {
            var e = $(this).children("i");
            e.trigger("click");
        });
    }
};
w.init();
class _api {
    constructor(e) {
        this.elem = e,
            this.json = {};
        var t = this;
        element.on("tab(" + this.elem + ")", function (e) {
            var a = $(this).attr("lay-id")
                , i = t.json[a]
                , n = $(this).children("span").text();
            $(".layui-breadcrumb>a").eq(0).text(i),
                $(".layui-breadcrumb>a").eq(1).text(n),
                $(".layui-side dd>a").parent().removeClass("layui-this");
            var l = $(".layui-side dd>a[href='" + a + "']");
            l.parent().addClass("layui-this"),
                l.parents(".layui-nav-item").addClass("layui-nav-itemed");
            var s = t.index(a);
            $(".layui-tab-title").stop().animate({
                scrollLeft: s
            }, 500),
                t.SetTab(a),
                $(".layui-tab-item.layui-show").removeClass("layui-show"),
                $('iframe[lay-id="' + a + '"]').parent().addClass("layui-show")
        }),
            element.on("tabDelete(" + this.elem + ")", function (e) {
                var a = $(this).parent().attr("lay-id");
                t.json[a] = void 0,
                    t.CloseTab(a)
            }),
            new Sortable($(".layui-tab-title")[0], {
                handle: "li",
                animation: 150,
                ghostClass: "blue-background-class",
                onEnd: function (e) {
                    var a = [];
                    $(".layui-tab-title>li").each(function (e) {
                        var t = {
                            id: $(this).attr("lay-id"),
                            indexs: e
                        };
                        a.push(t)
                    }),
                        t.sort("user_tab", a)
                }
            }),
            "true" == window.admin && this.menu(),
            this.isTab = !1
    }
    init(e = 0) {
        $(".layui-side dd>a").eq(e).trigger("click").parent().addClass("layui-this")
    }
    add(e, t = "标题", a = "系统设置") {
        if ("object" == typeof e) {
            var i = $(e).attr("href");
            t = $(e).text(),
                a = $(e).parents("dl").prev().text()
        } else
            i = e;
        var n = "1" == window.anim ? "layui-anim layui-anim-up" : "";
        return "javascript:;" == i ? (layer.msg("未设置跳转链接！", {
            icon: 3
        }),
            !1) : null != this.json[i] ? (element.tabChange(this.elem, i),
                !0) : (element.tabAdd(this.elem, {
                    title: '<span title="' + i + '">' + t + "</span>",
                    content: '<iframe frameborder="0" src="' + i + '" onload="App.removeLoad()" lay-id="' + i + '" class="' + n + '"></iframe>',
                    id: i
                }),
                    element.tabChange(this.elem, i),
                    $(".layui-breadcrumb>a").eq(0).text(a),
                    $(".layui-breadcrumb>a").eq(1).text(t),
                    this.json[i] = a,
                    this.load(),
                    void this.AddTab(i, t, a))
    }
    AddTab(e, t, a) {
        if (!this.isTab)
            return !1;
        $.ajax({
            url: api.url("AddTab", "?method="),
            type: "POST",
            dataType: "json",
            data: {
                url: e,
                title: t,
                nav: a
            },
            success: function (e) { },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    CloseTab(e) {
        $.ajax({
            url: api.url("CloseTab", "?method="),
            type: "POST",
            dataType: "json",
            data: {
                url: e
            },
            success: function (e) { },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    SetTab(e) {
        if (!this.isTab)
            return !1;
        $.ajax({
            url: api.url("SetTab", "?method="),
            type: "POST",
            dataType: "json",
            data: {
                type: "tab_url",
                value: e
            },
            success: function (e) { },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    index(e) {
        return $(".layui-tab-title>li[lay-id='" + e + "']").offset().left - (0 == $(".layui-side").offset().left ? 238 : 0) - 15 + "px"
    }
    load() {
        this.loade = $('<div class="load"><div class="load-icon"><span class="load-1"></span><span class="load-2"></span><span class="load-3"></span><span class="load-4"></span></div></div>'),
            $(".layui-tab-item.layui-show").append(this.loade)
    }
    removeLoad() {
        $(".load").fadeOut(500, function () {
            $(this).remove(),
                $(".refresh-this").removeClass("this")
        })
    }
    FullScreen(e = document.documentElement) {
        e.requestFullScreen && e.requestFullScreen(),
            e.mozRequestFullScreen && e.mozRequestFullScreen(),
            e.webkitRequestFullScreen && e.webkitRequestFullScreen()
    }
    ExitScreen(e = document) {
        e.exitFullscreen && e.exitFullscreen(),
            e.mozCancelFullScreen && e.mozCancelFullScreen(),
            e.msExitFullscreen && e.msExitFullscreen(),
            e.webkitCancelFullScreen && e.webkitCancelFullScreen(),
            e.webkitExitFullscreen && e.webkitExitFullscreen()
    }
    quit() {
        $.ajax({
            url: api.url("quit", "?method="),
            type: "POST",
            dataType: "json",
            data: {
                id: 100
            },
            beforeSend: function () {
                layer.msg("正在退出", {
                    icon: 16,
                    shade: .05,
                    time: !1
                })
            },
            success: function (e) {
                layer.msg(e.msg, {
                    icon: e.code
                });
                1 == e.code && location.reload()
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    dotRead() {
        $.ajax({
            url: "php/api.php?eventType=dotRead",
            type: "POST",
            dataType: "json",
            success: function (e) {
                1 == e.code && $(".message-dot").addClass("layui-hide")
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    message() {
        $.ajax({
            url: api.url("message", "?method="),
            type: "POST",
            dataType: "json",
            success: function (e) {
                if (5 == e.code)
                    return App.play("other", function () {
                        location.reload()
                    }),
                        layer.msg("您的账号在其他地方登录", {
                            icon: 3,
                            time: 4e3
                        }, function () {
                            location.reload()
                        }),
                        !1;
                if (1 == e.code) {
                    if ($(".message-dot").html(e.data.count).removeClass("layui-hide"),
                        0 == e.data.type) {
                        var t;
                        if (0 == e.data.body && (App.play("logon"),
                            layer.msg("有用户登录了平台"),
                            setTimeout(function () {
                                var e = parent.$("iframe[src='page/chat_user.php']");
                                e.length > 0 && (e[0].contentWindow.window.ChatMsg(),
                                    e[0].contentWindow.window.ChatUser())
                            }, 1e3)),
                            1 == e.data.body && (App.play("quit"),
                                layer.msg("有用户注销登录了"),
                                setTimeout(function () {
                                    var e = parent.$("iframe[src='page/chat_user.php']");
                                    e.length > 0 && (e[0].contentWindow.window.ChatMsg(),
                                        e[0].contentWindow.window.ChatUser())
                                }, 1e3)),
                            2 == e.data.body)
                            App.play("add"),
                                layer.msg("有好友申请信息"),
                                (t = parent.$("iframe[src='page/chat_add.php']")).length > 0 && t[0].contentWindow.window.init(),
                                (n = parent.$("iframe[src='page/chat_user.php']")).length > 0 && n[0].contentWindow.window.ChatAdd();
                        if (3 == e.data.body)
                            App.play("agree"),
                                layer.msg("有新的好友添加成功"),
                                (a = parent.$("iframe[src='page/chat_user.php']")).length > 0 && a[0].contentWindow.window.ChatUser(),
                                (t = parent.$("iframe[src='page/chat_add.php']")).length > 0 && t[0].contentWindow.window.init();
                        if (5 == e.data.body)
                            (a = parent.$("iframe[src='page/chat_user.php']")).length > 0 && (a[0].contentWindow.window.ChatUser(),
                                a[0].contentWindow.window.ChatMsg())
                    }
                    if (1 == e.data.type && 0 == e.data.body) {
                        App.play("msg"),
                            layer.msg("你有未读的私信");
                        var a, i = $("iframe[src='page/message.php']");
                        i.length > 0 && i[0].contentWindow.window.init(),
                            (a = parent.$("iframe[src='page/chat_user.php']")).length > 0 && a[0].contentWindow.window.ChatMsg()
                    }
                }
                var n;
                if ("message" == (n = $(".message").attr("class"))) {
                    var l = Number(window.msg_time);
                    setTimeout(function () {
                        App.message()
                    }, l)
                }
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    play(e, t) {
        var a = $('<audio src="mp3/' + e + '.mp3" autoplay="autoplay"></audio>');
        $("body").append(a),
            a.on("ended", function () {
                $(this).remove(),
                    t && t()
            })
    }
    sort(e, t) {
        $.ajax({
            url: api.url("user_tab" == e ? "SortTab" : "SortNav", "?method="),
            type: "POST",
            dataType: "json",
            data: {
                data: t,
                surface: e
            },
            success: function (e) {
                1 != e.code && console.log(e)
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    menu() {
        var e = this;
        api.menu(".layui-side .layui-nav-item>a", function (t) {
            var a = $(t).parent().attr("state");
            dropdown.render({
                elem: t,
                show: !0,
                trigger: "contextmenu",
                data: [{
                    title: "新增菜单",
                    id: "add"
                }, {
                    title: "修改菜单",
                    id: "edit"
                }, {
                    title: "删除菜单",
                    id: "del"
                }, {
                    type: "-"
                }, {
                    title: 0 == a ? "默认展开" : "默认折叠",
                    id: "state"
                }],
                click: function (i) {
                    "add" == i.id && e.editMenu(),
                        "edit" == i.id && e.editMenu(this.elem.parent().attr("data-id")),
                        "del" == i.id && e.delMenu(this.elem.parent().attr("data-id")),
                        "state" == i.id && e.stateMenu(this.elem.parent().attr("data-id"), a, t)
                },
                align: "left",
                style: "box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);"
            })
        }),
            api.menu(".layui-side .layui-nav-item dd>a", function (t) {
                dropdown.render({
                    elem: t,
                    show: !0,
                    trigger: "contextmenu",
                    data: [{
                        title: "新增项目",
                        id: "add"
                    }, {
                        title: "修改项目",
                        id: "edit"
                    }, {
                        title: "删除项目",
                        id: "del"
                    }],
                    click: function (t) {
                        "add" == t.id && e.editItem("", this.elem.parents("li").attr("data-id")),
                            "edit" == t.id && e.editItem(this.elem.attr("data-id"), this.elem.parents("li").attr("data-id")),
                            "del" == t.id && e.delItem(this.elem.attr("data-id"))
                    },
                    align: "left",
                    style: "box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);"
                })
            }),
            new Sortable($(".layui-side .layui-nav")[0], {
                handle: ".lay-sort",
                animation: 150,
                ghostClass: "blue-background-class",
                onEnd: function (t) {
                    var a = [];
                    $(".layui-side .layui-nav>li").each(function (e) {
                        var t = {
                            id: $(this).attr("data-id"),
                            indexs: e
                        };
                        a.push(t)
                    }),
                        e.sort("menu_list", a)
                }
            }),
            $(".layui-side .layui-nav dl").each(function () {
                var t = this;
                new Sortable(this, {
                    handle: ".lay-sort",
                    animation: 150,
                    ghostClass: "blue-background-class",
                    onEnd: function (a) {
                        var i = [];
                        $(t).find("a").each(function (e) {
                            var t = {
                                id: $(this).attr("data-id"),
                                indexs: e
                            };
                            i.push(t)
                        }),
                            e.sort("menu_node", i)
                    }
                })
            })
    }
    editMenu(e = "") {
        layer.open({
            type: 2,
            title: "" != e ? "修改菜单" : "新增菜单",
            area: ["400px", "350px"],
            maxmin: !1,
            content: "page/edit-menu.php?id=" + e,
            shade: .3
        })
    }
    delMenu(e) {
        $.ajax({
            url: api.url("MenuDel", "?method="),
            type: "POST",
            dataType: "json",
            data: {
                id: e
            },
            beforeSend: function () {
                layer.msg("正在加载", {
                    icon: 16,
                    shade: .05,
                    time: !1
                })
            },
            success: function (e) {
                layer.msg(e.msg, {
                    icon: e.code
                }),
                    1 == e.code && location.reload()
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    editItem(e = "", t) {
        layer.open({
            type: 2,
            title: "" != e ? "修改项目" : "新增项目",
            area: ["400px", "350px"],
            maxmin: !1,
            content: "page/edit-node.php?id=" + e + "&menu_id=" + t,
            shade: .3
        })
    }
    delItem(e) {
        $.ajax({
            url: api.url("ItemDel"),
            type: "POST",
            dataType: "json",
            data: {
                id: e
            },
            beforeSend: function () {
                layer.msg("正在加载", {
                    icon: 16,
                    shade: .05,
                    time: !1
                })
            },
            success: function (e) {
                layer.msg(e.msg, {
                    icon: e.code
                }),
                    1 == e.code && location.reload()
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    stateMenu(e, t, a) {
        $.ajax({
            url: api.url("stateMenu"),
            type: "POST",
            dataType: "json",
            data: {
                id: e,
                state: "1" == t ? 0 : 1
            },
            beforeSend: function () {
                layer.msg("正在加载", {
                    icon: 16,
                    shade: .05,
                    time: !1
                })
            },
            success: function (e) {
                if (layer.msg(e.msg, {
                    icon: e.code
                }),
                    1 == e.code) {
                    var i = "1" == t ? 0 : 1;
                    $(a).parent().attr("state", i),
                        1 == i ? $(a).parent().addClass("layui-nav-itemed") : $(a).parent().removeClass("layui-nav-itemed")
                }
            },
            error: e => layer.alert(e.responseText, {
                icon: 2
            })
        })
    }
    scroll(e) {
        layer.open({
            type: 1,
            title: !1,
            area: ["800px", "600px"],
            maxmin: !1,
            shade: 0,
            content: '<div class="layer-img"><img src="' + e + '" /></div>',
            success: function (e, t) {
                var a = e.find(".layer-img>img")
                    , i = a.parent()
                    , n = i.width()
                    , l = i.height()
                    , s = a.width()
                    , o = a.height()
                    , r = 1
                    , d = 80;
                s >= n && (a.width(n - d),
                    s = a.width(),
                    o = a.height(),
                    d = s / o),
                    o >= l && (a.height(l - d),
                        s = a.width(),
                        o = a.height()),
                    a.css({
                        left: n / 2 - s / 2,
                        top: l / 2 - o / 2
                    }),
                    $(document).off("mousewheel DOMMouseScroll mousedown touchstart"),
                    $(document).on("mousewheel DOMMouseScroll", ".layer-img", function (e) {
                        e.preventDefault();
                        var t = e.originalEvent.wheelDelta || -e.originalEvent.detail
                            , a = Math.max(-1, Math.min(1, t))
                            , i = $(this).children("img");
                        a < 0 ? r -= .1 : r += .1,
                            r = parseFloat(r),
                            i.width(s * r).height(o * r)
                    }),
                    $(document).on("mousedown touchstart", ".layer-img>img", function (e) {
                        e.preventDefault();
                        var t = $(this)
                            , a = t.position().left
                            , i = t.position().top
                            , n = e.pageX || Event.touches[0].clientX
                            , l = e.pageY || Event.touches[0].clientY;
                        $(document).on("mousemove.move touchmove.move", function (e) {
                            e.preventDefault();
                            var s = e.pageX || Event.touches[0].clientX
                                , o = e.pageY || Event.touches[0].clientY;
                            t.css({
                                left: a + (s - n),
                                top: i + (o - l),
                                right: "auto",
                                bottom: "auto"
                            })
                        }),
                            $(document).on("mouseup.move touchend.move", function () {
                                $(this).off("mousemove.move touchmove.move mouseup.move touchend.move")
                            })
                    })
            }
        })
    }
}
var App = new _api("tab");