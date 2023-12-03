<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>网站初始化</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="/dist/layui/css/layui.css">
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" href="css/logon.css" />
</head>

<body>
	<div class="bg">
		<div class="main">
			<div class="poster">
			</div>
			<div class="content">
				<div class="item layui-form this">
					<div class="head">
						<b class="title">初始化</b>
						<span class="tip">连接MySQL存储的网站数据</span>
					</div>
					<div class="body">
						<div class="layui-form-item">
							<div class="layui-input-block">
								<input type="text" name="servername" lay-verify="required" class="layui-input" placeholder="IP/网址" />
							</div>
						</div>
						<div class="layui-form-item">
							<div class="layui-input-block">
								<input type="text" name="username" lay-verify="required" class="layui-input" placeholder="用户名" />
							</div>
						</div>
						<div class="layui-form-item">
							<div class="layui-input-block">
								<input type="text" name="password" lay-verify="required" class="layui-input" placeholder="密码" />
							</div>
						</div>
						<div class="layui-form-item">
							<div class="layui-input-block">
								<input type="text" name="dbname" lay-verify="required" class="layui-input" placeholder="数据库名" />
							</div>
						</div>
						<div class="layui-form-item">
							<div class="layui-input-block">
								<input type="text" name="port" lay-verify="required" class="layui-input" placeholder="端口号" value="3306" />
							</div>
						</div>
						<div class="layui-form-item">
							<button class="layui-btn layui-btn-fluid layui-btn-normal" lay-submit lay-filter="submit">确定</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script src="/dist/layui/layui.js?v=20201111001"></script>
<script src="js/api.js"></script>
<script>
	form.on("submit(submit)", function(data) {
		$.ajax({
			url: api.url('init', '?method='),
			type: 'POST',
			dataType: 'json',
			data: data.field,
			beforeSend: function() {
				$("[lay-filter='submit']").prop('disabled', true);
				layer.msg("正在连接", {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: function(data) {
				layer.msg(data.msg, {
					icon: data.code
				}, function() {
					$("[lay-filter='submit']").prop('disabled', false);
					if (data.code == 1) {
						var url = location.href.split("?")[0];
						window.location.replace(url);
					}
				});
			},
			error: r => layer.alert(r.responseText, { icon: 2 })
		});
		return false;
	});

	document.onkeydown = function(e) {
		var keyCode = e.keyCode || e.which || e.charCode;
		var ctrlKey = e.ctrlKey || e.metaKey;
		if (keyCode == 13) {
			$(".layui-btn.layui-btn-fluid").trigger("click");
		}
	};
</script>

</html>