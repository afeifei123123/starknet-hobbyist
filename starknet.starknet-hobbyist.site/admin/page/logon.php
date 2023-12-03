<?php
class _web extends _api
{
	//提示信息
	public function placeholder()
	{
		return $this->sys['sms_state'] ? '手机号' : '用户名';
	}

	//是否显示验证码
	public function captcha()
	{
		$html = '<div class="layui-form-item">
					<label class="layui-form-label">
						<i class="layui-icon layui-icon-vercode"></i>
					</label>
					<div class="layui-input-block">
						<input type="text" name="captcha" lay-verify="captcha" class="layui-input" placeholder="图片验证码" />
						<img class="captcha" src="./?method=captcha" title="点击更换验证码" />
					</div>
				</div>';
		return $this->sys['captcha_state'] == '1' ? $html : '';
	}

	//是否显示记住密码
	public function recall()
	{
		$html = '<div class="layui-form-item">
					<div class="layui-input-block">
						<input type="checkbox" name="recall" title="记住密码" lay-skin="primary" />
					</div>
				</div>';
		return $this->sys['recall_state'] == '1' ? $html : '';
	}

	//是否开启密码找回
	public function retpawd()
	{
		return $this->sys['sms_state'] == '1' ? '' : 'layui-hide';
	}

	//是否开启注册
	public function register($type = 0)
	{
		if ($this->sys['sms_state'] == 0) {
			return '';
		}
		$html = '<div class="layui-form-item">
					<label class="layui-form-label">
						<i class="layui-icon layui-icon-vercode"></i>
					</label>
					<div class="layui-input-block">
						<input type="text" name="smscode" lay-verify="smscode" class="layui-input" placeholder="短信验证码"/>
						<button class="getSmsCode" data-type="' . $type . '">发送验证码</button>
					</div>
				</div>';
		return $this->sys['register_state'] == '1' ? $html : '';
	}

	//验证用户名类型
	public function type()
	{
		return $this->sys['sms_state'] ? 'phone' : 'username';
	}

	//是否存在跳转地址
	public function url()
	{
		return  isset($_GET['url']) ? $_GET['url'] : '';
	}
};
$web = new _web(1);
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title><?php echo $web->sys['title']; ?>-登录</title>
	<meta name="renderer" content="webkit" />
	<meta name="Keywords" content="<?php echo $web->sys['Keywords']; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="css/logon.css?v=<?php echo $web->v; ?>" />
</head>

<body>
	<div class="bg">
		<div class="main">
			<div class="left">
				<img src="<?php echo $web->sys['logon_img'] ?>" />
			</div>
			<div class="right">
				<div class="item layui-form this">
					<div class="head">
						<b class="title">登录</b>
						<span class="tip <?php echo $web->sys['register_state'] == '0' ? 'layui-hide' : '' ?>">还没有账号？</span>
						<a class="link <?php echo $web->sys['register_state'] == '0' ? 'layui-hide' : '' ?>" href="1">去注册</a>
					</div>
					<div class="body">
						<div class="layui-form-item">
							<label class="layui-form-label">
								<i class="layui-icon layui-icon-username"></i>
							</label>
							<div class="layui-input-block">
								<input type="text" name="username" lay-verify="username" class="layui-input" placeholder="<?php echo $web->placeholder(); ?>" />
							</div>
						</div>
						<div class="layui-form-item">
							<label class="layui-form-label">
								<i class="layui-icon layui-icon-password"></i>
							</label>
							<div class="layui-input-block">
								<input type="password" name="password" lay-verify="password" class="layui-input" placeholder="密码" />
							</div>
						</div>
						<?php echo $web->captcha(); ?>
						<div class="layui-form-item">
							<button class="layui-btn layui-btn-fluid layui-btn-normal" lay-submit lay-filter="logon-submit">登录</button>
						</div>
						<?php echo $web->recall(); ?>
						<div class="other <?php echo $web->retpawd(); ?>">
							<span class="tip <?php echo $web->sys['retpawd_state'] == '0' ? 'layui-hide' : '' ?>">我已经忘记密码，</span>
							<a class="link <?php echo $web->sys['retpawd_state'] == '0' ? 'layui-hide' : '' ?>" href="2">去找回密码</a>
						</div>
					</div>
				</div>
				<div class="item layui-form">
					<div class="head">
						<b class="title">注册</b>
						<span class="tip">已有账号，</span>
						<a class="link" href="0">去登录</a>
					</div>
					<div class="body">
						<div class="layui-form-item">
							<label class="layui-form-label">
								<i class="layui-icon layui-icon-username"></i>
							</label>
							<div class="layui-input-block">
								<input type="text" name="username" lay-verify="<?php echo $web->type(); ?>" class="layui-input" placeholder="<?php echo $web->placeholder(); ?>" />
							</div>
						</div>
						<?php echo $web->register(0); ?>
						<div class="layui-form-item">
							<label class="layui-form-label">
								<i class="layui-icon layui-icon-password"></i>
							</label>
							<div class="layui-input-block">
								<input type="password" name="password" lay-verify="password" class="layui-input" placeholder="密码（6-16位字符，支持数字、字母，区分大小写）" />
							</div>
						</div>
						<div class="layui-form-item">
							<button class="layui-btn layui-btn-fluid layui-btn-normal" lay-submit lay-filter="register-submit">注册</button>
						</div>
						<div class="agreement">
							<span>注册即代表同意</span>
							<a class="link" href="page/agreement.php" target="_blank">《用户协议》</a>
						</div>
					</div>
				</div>
				<div class="item layui-form">
					<div class="head">
						<b class="title">找回密码</b>
					</div>
					<div class="body">
						<div class="layui-form-item">
							<div class="layui-input-block">
								<input type="text" name="username" lay-verify="<?php echo $web->type(); ?>" class="layui-input" placeholder="手机号" />
							</div>
						</div>
						<?php echo $web->register(1); ?>
						<div class="layui-form-item">
							<div class="layui-input-block">
								<input type="password" name="password" lay-verify="password" class="layui-input" placeholder="密码（6-16位字符，支持数字、字母，区分大小写）" />
							</div>
						</div>
						<div class="layui-form-item">
							<button class="layui-btn layui-btn-fluid layui-btn-normal" lay-submit lay-filter="retpawd-submit">重置密码</button>
						</div>
						<div class="other">
							<span class="tip">我想起来密码了，</span>
							<a class="link" href="0">去登录</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
	api.verifyRes = function(elem, name, is) {
		if (!is) {
			var text = name == 'username' ? '用户名格式不正确' : '密码格式不正确';
			var item = $('<div class="error">' + text + '</div>');
			$(elem).parents('.item').append(item);
			setTimeout(function() {
				item.fadeOut(500, function() {
					$(this).remove();
				});
			}, 2000);
		}
	};
	//提交登录事件
	form.on("submit(logon-submit)", function(data) {
		$.ajax({
			url: api.url('logon', '?method='),
			type: 'POST',
			dataType: 'json',
			data: data.field,
			beforeSend: function() {
				$("[lay-filter='logon-submit']").prop('disabled', true);
				layer.msg("正在登录", {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: function(data) {
				layer.msg(data.msg, {
					icon: data.code
				}, function() {
					$("[lay-filter='logon-submit']").prop('disabled', false);
					if (data.code == 1) {
						//这里是告诉网页登录成功后跳转到的地址
						var c = $("[name=recall]");
						if (c.length > 0) {
							var s = c.prop("checked");
							if (s) {
								localStorage.setItem('username' + location.pathname, $('[name=username]').eq(0).val());
								localStorage.setItem('password' + location.pathname, $('[name=password]').eq(0).val());
							} else {
								localStorage.removeItem('username' + location.pathname);
								localStorage.removeItem('password' + location.pathname);
							}
						}
						var url = '<?php echo $web->url(); ?>';
						if (url != "") {
							window.location.replace(url);
							return true;
						}
						//这里是告诉网页登录成功后是否刷新上一层的网页
						if (window.top != window.self) {
							parent.layer.closeAll();
							parent.window.location.reload();
							return true;
						}
						window.location.reload();
					}
				});
			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
		return false;
	});

	//提交注册
	form.on("submit(register-submit)", function(data) {
		$.ajax({
			url: api.url('register', '?method='),
			type: 'POST',
			dataType: 'json',
			data: data.field,
			beforeSend: function() {
				$("[lay-filter='register-submit']").prop('disabled', true);
				layer.msg("正在注册", {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: function(data) {
				layer.msg(data.msg, {
					icon: data.code
				}, function() {
					$("[lay-filter='register-submit']").prop('disabled', false);
					if (data.code == 1) {
						$(".item.this").removeClass("this");
						$(".item").eq(0).addClass("this");
					}
				});
			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
		return false;
	});

	//找回密码
	form.on("submit(retpawd-submit)", function(data) {
		$.ajax({
			url: api.url('retpawd', '?method='),
			type: 'POST',
			dataType: 'json',
			data: data.field,
			beforeSend: function() {
				$("[lay-filter='retpawd-submit']").prop('disabled', true);
				layer.msg("正在验证", {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: function(data) {
				layer.msg(data.msg, {
					icon: data.code
				}, function() {
					$("[lay-filter='retpawd-submit']").prop('disabled', false);
					if (data.code == 1) {
						$(".item.this").removeClass("this");
						$(".item").eq(0).addClass("this");
					}
				});
			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
		return false;
	});

	function init() {
		//超链接点击事件
		$(document).on("click", ".link", function(e) {
			var index = Number($(this).attr("href"));
			if (!isNaN(index)) {
				e.preventDefault();
				$(".item.this").removeClass("this");
				$(".item").eq(index).addClass("this");
			}
		});

		//更换验证码图片
		$(document).on("click", ".captcha", function() {
			var src = "?method=captcha&v=" + Math.random();
			$(this).attr("src", src);
		});

		//监听回车事件 提交的是可视的页面按钮
		$(document).on("keydown", function(e) {
			var keyCode = e.keyCode || e.which || e.charCode,
				ctrlKey = e.ctrlKey || e.metaKey;
			if (keyCode == 13) {
				var box = $(".item.this");
				box.find(".layui-btn.layui-btn-fluid").click();
			}
		});

		//发送验证码
		$(".getSmsCode").click(function() {
			getSmsCode($(this));
		});

		if (localStorage.getItem('username' + location.pathname) != undefined && localStorage.getItem('password' + location.pathname) != undefined) {
			var u = localStorage.getItem('username' + location.pathname),
				p = localStorage.getItem('password' + location.pathname),
				box = $('.item.this'),
				c = $("[name=recall]");
			if (c.length > 0) {
				box.find('[name=username]').val(u);
				box.find('[name=password]').val(p);
				c.prop('checked', true);
				form.render('checkbox');
			}
			var auto = "<?php echo $web->sys['autologon_state']; ?>";
			if (auto == "1") {
				var box = $(".item.this");
				let time = <?php echo $web->sys['autologon_time']; ?>;
				layer.msg('自动登录', {
					icon: 16,
					time: false
				});
				setTimeout(function() {
					box.find(".layui-btn.layui-btn-fluid").trigger("click");
				}, time);
			}
		}
	}

	function getSmsCode(btn) {
		var type = btn.attr("data-type"),
			elem = btn.parents(".layui-form").find("[name=username]"),
			tel = elem.val();
		if (tel == undefined || tel == "") {
			layer.msg('请填写手机号码', {
				icon: 3
			});
			elem.focus();
			return false;
		}
		if (!/^1[3456789]\d{9}$/.test(tel)) {
			layer.msg('手机号码格式不正确', {
				icon: 3
			});
			elem.focus();
			return false;
		}
		$.ajax({
			url: api.url('sendSmsCode', '?method='),
			type: 'POST',
			dataType: 'json',
			data: {
				tel: tel,
				type: type
			},
			beforeSend: function() {
				layer.msg("正在发送", {
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
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
	}
	init();
</script>

</html>