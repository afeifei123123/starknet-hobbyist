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

    // 上传文件
    public function _uploads()
    {
        $this->_init();
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
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <title>上传文件到服务器</title>
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        body {
            padding: 0;
        }

        .header {
            height: 50px;
            line-height: 50px;
            padding: 0px 20px;
        }

        .content {
            position: absolute;
            top: 50px;
            left: 0;
            right: 0;
            bottom: 50px;
            overflow: auto;
        }

        .footer {
            height: 50px;
            left: 0;
            right: 0;
            text-align: right;
            bottom: 0;
            position: absolute;
            padding: 0px 20px;
            line-height: 40px;
        }

        .success td::before {
            content: "";
            display: block;
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background-color: #00c7ff1a;
        }

        .accept {
            position: fixed;
            left: 5px;
            right: 5px;
            top: 5px;
            bottom: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            text-align: center;
            font-size: 20px;
            padding-top: 170px;
            border: 2px dotted #1e90ff;
            font-weight: bold;
            color: #999999;
            display: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-plug-success" id="select">选择文件</button>
    </div>
    <div class="content">
        <div class="layui-upload-list">
            <table class="layui-table" lay-skin="line" lay-size="sm">
                <thead>
                    <tr>
                        <th>文件名</th>
                        <th>大小</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="list">
                    <!-- 上传文件列表 -->
                </tbody>
            </table>
            <div class="accept">拖入文件</div>
        </div>
    </div>
    <div class="layui-footer layui-nobox">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="upload">开始上传</button>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" id="cancel">取消上传</button>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    window.getHref = function(str) {
        return decodeURIComponent(location.href.split(str + "=")[1].split("&")[0]);
    };
    var elem = $('#list'),
        uploadListIns = upload.render({
            elem: '#select,.accept',
            url: api.url('uploads'),
            accept: 'file',
            multiple: true,
            auto: false,
            bindAction: '#upload',
            data: {
                path: getHref("path"),
                serverId: getHref("serverId")
            },
            choose: function(obj) {
                var files = this.files = obj.pushFile();
                obj.preview(function(index, file, result) {
                    var tr = $(['<tr id="upload-' + index + '">', '<td>' + file.name +
                        '</td>', '<td>' + (file.size / 1024).toFixed(
                            1) + 'kb</td>', '<td>等待上传</td>', '<td>',
                        '<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>',
                        '<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>',
                        '</td>', '</tr>'
                    ].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function() {
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function() {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = '';
                    });
                    elem.append(tr);
                });
            },
            done: function(data, index, upload) {
                if (data.code == 1) { //上传成功
                    var tr = elem.find('tr#upload-' + index),
                        tds = tr.children();
                    tr.addClass("success");
                    tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                    tds.eq(3).html(''); //清空操作
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                }
                this.error(index, upload);
            },
            error: function(index, upload) {
                var tr = elem.find('tr#upload-' + index),
                    tds = tr.children();
                tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
                tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
            },
            allDone: function(obj) {
                window.parent.frames.reload("file");
                layer.msg("上传成功" + obj.successful + "个，失败" + obj.aborted + "个", {
                    icon: 1
                });
            }
        });
    $(document).on("dragover", function(e) {
        e.preventDefault();
    });
    $(document).on("drop", function(e) {
        e.preventDefault();
    });
    $(".accept").click(function(event) {
        event.preventDefault();
    });
    $(".content").on("dragover", function() {
        $(".accept").show(0);
    });
    $(".accept").on("drop", function() {
        $(this).hide(0);
    });
    $(".accept").on("dragleave", function() {
        $(this).hide(0);
    });

    $("#cancel").click(function() {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    });
</script>

</html>