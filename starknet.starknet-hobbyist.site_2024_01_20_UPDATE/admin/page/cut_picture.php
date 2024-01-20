<?php
include '../php/api.php';
$web = new _api(1);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>裁剪图片</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/cropper.min.css?v=<?php echo $web->v; ?>" />
    <style>
        .img-box {
            height: 400px;
        }

        .img {
            width: 250px;
        }
    </style>
</head>

<body class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">上传素材</label>
        <div class="layui-input-inline">
            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal select">选择图片</button>
        </div>
    </div>
    <hr />
    <div class="img-box">
        <img class="img" />
    </div>
    <br />
    <div class="layui-footer layui-nobox">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal rotate" value="-90">
            <span>左旋转</span>
        </button>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal rotate" value="90">
            <span>右旋转</span>
        </button>
        <button class="layui-btn layui-btn-normal layui-btn-sm store" lay-submit lay-filter="submit">保存</button>
        <button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script>
    var jQuery = layui.$;
</script>
<script src="../js/cropper.min.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    class _api {
        constructor(obj) {
            var btn = $(obj.btn),
                elem = parent.cut,
                ext = elem.attr("ext"),
                src = elem.children("img").attr("src"),
                path = elem.attr("path"),
                del = elem.attr("del"),
                accept = ext != undefined ? ext :
                "image/gif,image/jpeg,image/jpg,image/png,image/svg,image/ico",
                file = $(`<input type="file" accept="${accept}"/>`),
                seft = this;
            this.elem = elem;
            btn.click(function() {
                file.click();
                file.change(function() {
                    var f = $(this)[0].files;
                    seft.init(f[0]);
                });
            });
            $('.rotate').click(function() {
                var value = Number($(this).val());
                seft.img.cropper('rotate', value);
            })
        }

        init(f) {
            var url = window.URL.createObjectURL(f),
                img = $('.img'),
                src = img.attr('src');
            this.img = img;
            if (src != undefined) {
                img.cropper('replace', url);
                return false;
            }
            img.attr('src', url);
            var w = this.elem.width();
            var h = this.elem.height();
            var options = {
                modal: true,
                guides: true,
                background: true,
                dragCrop: false,
                movable: false,
                resizable: false,
                aspectRatio: w / h,
                crop: function(data) {
                    //console.log(data);
                }
            };
            img.cropper(options);
        }

        store() {
            var canvas = this.img.cropper('getCroppedCanvas'),
                base64 = canvas.toDataURL(),
                elem = this.elem,
                src = elem.children("img").attr("src"),
                path = elem.attr("path"),
                del = elem.attr("del"),
                ext = elem.attr("ext"),
                accept = ext != undefined ? ext :
                "image/gif,image/jpeg,image/jpg,image/png,image/svg,image/ico",
                file = $(`<input type="file" accept="${accept}"/>`),
                formData = new FormData(),
                f = this.Base64ToBlob(base64, '上传图片.png');
            formData.append('file', f);
            if (src != undefined && src.indexOf("http") == -1) {
                formData.append("src", src);
            }
            if (path != undefined) {
                formData.append("path", path);
            }
            $.ajax({
                url: api.url('upload', '../?method='),
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    layer.msg('<span class="layer-load">正在上传</span>', {
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
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                    if (data.code != 1) {
                        return false;
                    }
                    var url = data.data.host;
                    if (src != undefined && src.indexOf("http") == -1) {
                        url = data.data.url;
                    }
                    parent.api.cut && parent.api.cut(elem, data);
                    elem.html('<img src="' + url + '" />');
                    elem.parents(".layui-form-item").find("input").val(data
                        .data.host);
                    if (del != undefined) {
                        elem.append(
                            '<div class="upload-del"><span class="del-file">删除</span></div>'
                        );
                    }
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                },
                error: r => layer.alert(r.responseText, { icon: 2 })
            });
        }

        Base64ToBlob(base64Str, fileName) {
            var arr = base64Str.split(','),
                mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]),
                len = bstr.length,
                ab = new ArrayBuffer(len),
                u8arr = new Uint8Array(ab);
            while (len--) {
                u8arr[len] = bstr.charCodeAt(len)
            };
            return new File([u8arr], fileName, {
                type: mime
            })
        }
    };
    setTimeout(() => {
        var App = new _api({
            btn: '.select'
        });
        $(".store").click(function() {
            App.store();
        })
    }, 500);
</script>

</html>