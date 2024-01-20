<?php
include '../php/api.php';
class _web extends _api
{
    //初始化
    public function _init()
    {

        $dir = "../upload";
        $_REQUEST['path'] = $dir . $this->is('path');
        $_REQUEST['local'] = $dir . $this->is('local');
    }


    // 获取文件图标
    public function _icon($file)
    {
        $ext = substr(strrchr($file, '.'), 1);
        $f = "../images/file.png";
        if ($ext == false) {
            return $f;
        }
        $a = ['jpg', 'gif', 'png', 'ico', 'jpeg', 'bmp', 'svg', 'webp'];
        if (in_array($ext, $a)) {
            return $_REQUEST['path'] . $file;
        }
        $a = "../images/{$ext}.png";
        if (is_file($a)) {
            return $a;
        }
        return $f;
    }

    // 删除目录
    public function _file_rmdir($dirname, $self = true)
    {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }
        $dir = dir($dirname);
        if ($dir) {
            while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $this->_file_rmdir($dirname . '/' . $entry);
            }
        }
        $dir->close();
        $self && rmdir($dirname);
    }

    // 获取文件列表
    public function _data()
    {
        $this->ajax(["path", "limit", "page"]);
        $path = $_REQUEST["path"];
        $limit = isset($_REQUEST["limit"]) ? intval($_REQUEST["limit"]) : 100;
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $start = ($page - 1) * $limit;
        $dir = iconv("utf-8", "gb2312//IGNORE", $path);
        if (!is_dir($dir)) {
            $json = ["code" => 0, "msg" => "目录不存在", "data" => [], "count" => 0];
            $this->res($json);
        }
        $arr = scandir($dir);
        $data = [];
        foreach ($arr as $key) {
            $name = iconv('gbk', 'utf-8', $key);
            if ($name != "." && $name != ".." && !strstr($name, " ")) {
                $name = iconv('gbk', 'utf-8', $key);
                $file = $path . $name;
                $type = is_dir(iconv("utf-8", "gb2312//IGNORE", $file)) ? "dir" : "file";
                $icon = $type == "dir" ? "../images/dir.png" : $this->_icon($name);
                $typeName = $type == "dir" ? "目录" : "文件";
                $updateDate = date("Y-m-d H:i:s", filectime(iconv("utf-8", "gb2312//IGNORE", $file)));
                $size = $type == "dir" ? "-" : round((filesize(iconv("utf-8", "gb2312//IGNORE", $file)) / 1024), 2) . "KB";
                $data[] = ["name" => $name, "type" => $type, "icon" => $icon, "typeName" => $typeName, "updateDate" => $updateDate, "size" => $size, "file" => $file];
            }
        }
        $count = count($data);
        $data = array_slice($data, $start, $limit);
        $json = ["code" => 0, "msg" => "调试成功", "data" => $data, "count" => $count];
        $this->res($json);
    }

    // 删除文件 or 目录
    public function _del()
    {
        $this->ajax(["item", "path"]);
        $path = $_REQUEST["path"];
        $item = $_REQUEST["item"];
        if (!is_array($item)) {
            $this->res("item请传递数组格式", "3");
        }
        $s_dir = $s_file = 0;
        foreach ($item as $key) {
            $name = $key["name"];
            $type = $key["type"];
            $file = iconv("utf-8", "gb2312//IGNORE", $path . $name);
            if ($type == "dir") {
                $this->_file_rmdir($file);
                $s_dir += 1;
            } else {
                if (is_file($file)) {
                    unlink($file);
                    $s_file += 1;
                }
            }
        }
        $icon = "成功删除目录{$s_dir}个，文件{$s_file}个";
        $this->res($icon, "1");
    }

    // 重命名文件 or 目录
    public function _rename()
    {
        $this->ajax(["path", "outName", "name"]);
        $path = $_REQUEST["path"];
        $name = $_REQUEST["name"];
        $outName = $_REQUEST["outName"];
        if (file_exists(iconv("utf-8", "gb2312//IGNORE", $path . $name))) {
            $this->res("文件或目录已存在", "3");
        }
        $res = rename(iconv("utf-8", "gb2312//IGNORE", $path . $outName), iconv("utf-8", "gb2312//IGNORE", $path . $name));
        if (!$res) {
            $this->res("修改失败", "3");
        }
        $this->res("修改成功", "1");
    }

    // 上传文件
    public function _uploads()
    {
        $this->ajax(["path"]);
        $path = $_REQUEST["path"];
        $count = count($_FILES);
        if ($count == 0) {
            $this->res("请选择文件", "3");
        }
        $local = $_FILES['file']["tmp_name"];
        $error = $_FILES['file']["error"];
        $file = iconv("utf-8", "gb2312//IGNORE", $path . $_FILES['file']["name"]);
        if ($error != 0) {
            exit($this->file_error($error));
        }
        $move = move_uploaded_file($local, $file);
        if ($move) {
            $this->res("文件上传成功！", "1");
        }
        exit("文件上传失败");
    }

    // 下载文件
    public function _download()
    {
        $this->ajax(["path", "name"]);
        $name = $_REQUEST["name"];
        $path = $_REQUEST["path"];
        $local = iconv("utf-8", "gb2312//IGNORE", $path . $name);
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: " . filesize($local));
        Header("Content-Disposition: attachment; filename=" . $name);
        echo file_get_contents($local);
        exit();
    }
}
$web = new _web(2, "id", false, true);
$web->_init();
$web->method();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <title>附件管理</title>
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>">
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/file.css?v=<?php echo $web->v; ?>" />
</head>

<body>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-msg">
                        <i class="layui-icon layui-icon-tips"></i>
                        <p>温馨提示：您可以在此管理网站用户上传的附件，包括替换、下载、删除、重命名等操作。</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div class="layui-form-item">
                        <label class="layui-form-label">
                            <span>上传目录</span>
                            <i class="layui-icon layui-icon-more-vertical"></i>
                        </label>
                        <div class="layui-input-block">
                            <div name="path" class="layui-input path">
                                <span class="dir">/</span>
                            </div>
                            <input type="text" class="layui-input pathWord" />
                        </div>
                    </div>
                </div>
                <div class="layui-card-body">
                    <table id="file" lay-filter="file"></table>
                </div>
            </div>
            <div class="upload-box">
                <span>上传文件到当前目录下</span>
                <!-- 文件上传 -->
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script type="text/html" id="file_tool">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="previous">
            <i class="layui-icon layui-icon-left"></i>
            <span>上一级</span>
        </button>
        <button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="renovate">
            <i class="layui-icon layui-icon-refresh-1"></i>
            <span>刷新</span>
        </button>
        <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="New">
            <i class="layui-icon layui-icon-add-1"></i>
            <span> 新建</span>
        </button>
        <button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="upload">
            <i class="layui-icon layui-icon-upload"></i>
            <span>上传</span>
        </button>
        <button class="layui-btn layui-btn-sm layui-btn-plug-danger" lay-event="Del">
            <i class="layui-icon layui-icon-delete"></i>
            <span>删除</span>
        </button>
    </div>
</script>
<script type="text/html" id="toolTpl">
    <div class="toolElem">
        <a lay-event="rename" class="rename layui-table-link">
            <i class="layui-icon layui-icon-edit"></i>
            <span>重命名</span>
        </a>
        <span class="{{ d.type == 'dir' ? 'layui-hide' : '' }}">|</span>
        <a lay-event="download" class="download {{ d.type == 'dir' ? 'layui-hide' : '' }}">
            <i class="layui-icon layui-icon-download-circle"></i>
            <span>下载</span>
        </a>
        <span>|</span>
        <a lay-event="del_file" class="del_file layui-table-del">
            <i class="layui-icon layui-icon-delete"></i>
            <span>删除</span>
        </a>
    </div>
</script>
</script>
<script>
    window.getPath = function() {
        return $("[name=path]").text().replace(/\s+|[\r\n]/g, "");
    };
    window.where = function() {
        var json = {
            path: getPath()
        };
        return json;
    };
    window.reload = function() {
        var id = 'file';
        table.reload(id, {
            where: where()
        });
        setTimeout(function() {
            $(".pathWord").val(getPath());
        }, 100);
    };
    window.getWidth = function(name) {
        let item = {
            '文件名': [300, 100],
            '类型': [100, 100],
            '修改时间': [170, 100]
        };
        if (/Mobi|Android|iPhone/i.test(navigator.userAgent)) {
            var width = item[name][1];
        } else {
            var width = item[name][0];
        }
        return width;
    };


    //渲染数据
    table.render({
        elem: "#file",
        url: api.url('data'),
        title: "文件管理",
        toolbar: "#file_tool",
        skin: 'nob',
        where: where(),
        page: true,
        cols: [
            [{
                type: 'checkbox'
            }, {
                field: "name",
                title: "文件名",
                width: getWidth('文件名'),
                templet: d => {
                    return `<a lay-event="name"><img src="${d.icon}" class="icon" />${d.name}</a>`;
                }
            }, {
                field: "typeName",
                title: "类型",
                width: 100
            }, {
                field: "updateDate",
                title: "修改时间",
                width: 170,
                sort: true
            }, {
                field: "size",
                title: "大小",
                width: 100,
                sort: true
            }, {
                title: "操作",
                templet: '#toolTpl',
                minWidth: 250,
                //fixed: "right"
            }]
        ]
    });
    //当前服务器ID
    window.serverId = function() {
        return '';
    };

    window.server = function() {
        return "file";
    };
    window.renderPath = function(path) {
        var item = "";
        var arr = path.split("/");
        for (var key in arr) {
            item += '<span class="dir">' + arr[key] + '<i>/</i></span>';
        }
        $(".path").html(item);
        return true;
    };

    //默认api地址
    window.apiHost = function() {
        return server();
    };



    // 初始化
    window.init = function() {
        renderPath("");
        //目录选择
        $(document).on("click", ".path .dir", function(e) {
            if (getPath() != "") {
                $(this).nextAll().remove();
                reload("file");
            } else {
                layer.msg('目录不存在');
            }
        });

        $(document).on("click", ".path", function(e) {
            var name = $(e.target).attr("name");
            if (name == "path") {
                $(".pathWord").show(0).val(getPath()).focus();
            }
        });

        $(document).on("click", ".path", function(e) {
            var name = $(e.target).attr("name");
            if (name == "path") {
                var path = getPath();
                //path.substr(0, path.length - 1)
                $(".pathWord").show(0).focus().val(path == "/" ? path : (path.substr(0, path
                    .length - 1)));
            }
        });

        $(document).on("blur", ".pathWord", function(e) {
            $(this).hide();
        });
        $(document).on("keydown", ".pathWord", function(e) {
            if (e.keyCode == 13) {
                $(this).blur();
                renderPath(this.value == "/" ? "" : this.value);
                reload("file");
            }
        });

        $(document).on("dragover", function(e) {
            e.preventDefault();
        });
        $(document).on("drop", function(e) {
            e.preventDefault();
        });

        $(".layui-card").on("dragover", function(e) {
            e.preventDefault();
            $(".upload-box").show(0);
        });

        $(".upload-box").on("dragleave", function(e) {
            e.preventDefault();
            $(this).hide(0);
        });
        $(".upload-box").on("drop", function(e) {
            $(this).hide(0);
        });

        $(".search-word").focus(function() {
            $(".search-list").show(0);
        });

        $(".search-btn").click(function() {
            search();
        });

        $(".search-word").keydown(function(e) {
            if (e.keyCode == 13) {
                search();
            }
        });

        $(document).on("mousedown", ".layui-border-box", function() {
            $(".search-list").hide(0);
        });
    };
    init();



    //工具栏
    table.on("toolbar(file)", function(obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        switch (obj.event) {
            case "Del":
                Del(checkStatus);
                break;
            case "previous":
                previous();
                break;
            case "renovate":
                reload("file");
                break;
            case "New":
                New();
                break;
            case "file_tzip":
                file_tzip(checkStatus);
                break;
            case "upload":
                upload_file();
                break;
        };
    });

    //行内工具栏
    table.on('tool(file)', function(obj) {
        var data = obj.data;
        switch (obj.event) {
            case "name":
                openFile(obj);
                break;
            case "rename":
                rename(obj);
                break;
            case "download":
                download(obj);
                break;
            case "del_file":
                del_files(obj);
                break;
            case "file_wzip":
                file_wzip(obj);
                break;
            case "get_zip":
                get_zip(obj);
                break;
        };
    });

    // 文件上传
    upload.render({
        elem: '.upload-box',
        url: api.url('uploads'),
        accept: "file",
        multiple: true,
        before: function(obj) {
            layer.msg('<span class="loading">上传中</span>', {
                icon: 16,
                shade: 0.2,
                time: false
            });
        },
        data: {
            path: function() {
                return $('[name=path]').text().replace(/	/g, '').replace(/\n/g, "");
            },
            serverId: serverId()
        },
        done: function(data, index, upload) {
            $(".loading").text("第" + (index + 1) + "个文件上传成功");
        },
        error: function(index, upload) {
            $(".loading").text("第" + (index + 1) + "个文件上传失败");
        },
        allDone: function(obj) {
            reload("file");
            layer.msg("上传成功" + obj.successful + "个，失败" + obj.aborted + "个", {
                icon: 6
            });
        }
    }); //单文件上传

    //打开目录 或者修改文件
    function openFile(obj) {
        if (obj.data.type == "dir") {
            //如果是目录
            var name = obj.data.name;
            if (getPath() != "") {
                var dir = '<span class="dir">' + name + '<i>/</i></span>';
                $("[name=path]").append(dir);
                reload("file");
            }
        } else {
            //如果是文件
            var name = obj.data.name;
            var ext = name.split('.').pop().toLowerCase();
            if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'ico') {
                var url = "upload" + getPath() + name;
                parent.App.scroll(url);
                return true;
            }
            layer.msg('文件不支持预览', {
                icon: 3
            });
        }
    }

    //新建文件or目录
    function New() {
        layer.open({
            type: 2,
            title: "新建文件or目录",
            area: ["400px", "250px"],
            maxmin: false,
            content: "upload_add.php?path=" + getPath() + "&serverId=" + serverId() +
                "&type=" + server()
        });
    }

    //上一级
    function previous() {
        if (getPath() != "/") {
            $("[name=path] .dir:last").remove();
            reload("file");
        } else {
            layer.msg('无上级目录', {
                icon: 3
            });
        }
    }

    //重命名
    function rename(obj) {
        layer.prompt({
            title: '重命名',
            formType: 0,
            value: obj.data.name
        }, function(name, index) {
            if (name != obj.data.name) {
                $.ajax({
                    url: api.url('rename'),
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        layer.msg("正在修改", {
                            icon: 16,
                            shade: 0.05,
                            time: false
                        });
                    },
                    data: {
                        name: name,
                        outName: obj.data.name,
                        path: getPath(),
                        serverId: serverId()
                    },
                    success: function(data) {
                        layer.msg(data.msg, {
                            icon: data.code
                        });
                        if (data.code == 1) {
                            layer.close(index);
                            reload("file");
                        }
                    },
                    error: r => layer.alert(r.responseText, {
                        icon: 2
                    })
                });
            } else {
                layer.close(index);
            }
        });
    }

    function download(obj) {
        var url = api.url('download') + "&name=" + obj.data.name +
            "&path=" +
            getPath() +
            "&serverId=" + serverId();
        location.href = url;
    }

    //删除文件
    function del_files(obj) {
        layer.confirm('确定删除此文件吗？', function() {
            var data = [{
                name: obj.data.name,
                type: obj.data.type
            }];
            del_file(data);
        });
    }


    //上传文件
    function upload_file() {
        layer.open({
            type: 2,
            title: "上传文件",
            area: ["600px", "500px"],
            maxmin: true,
            //shade: 0,
            content: "upload_file.php?path=" + getPath() + "&serverId=" + serverId() +
                "&type=" + server()
        });
    }

    // 删除单个文件
    function del_file(data) {
        $.ajax({
            url: api.url('del'),
            type: 'POST',
            dataType: 'json',
            data: {
                item: data,
                path: getPath(),
                serverId: serverId()
            },
            beforeSend: function() {
                layer.msg("删除中", {
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
                    reload("file");
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }

    //删除多个文件
    function Del(checkStatus) {
        var data = checkStatus.data;
        if (data.length > 0) {
            layer.confirm('确定删除选中的文件吗？', function() {
                del_file(data);
            });
        } else {
            layer.msg("未选择文件", {
                icon: 3
            });
        }
    }
</script>

</html>