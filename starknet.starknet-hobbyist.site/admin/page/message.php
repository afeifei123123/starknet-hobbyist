<?php
include '../php/api.php';
class _web extends _api
{
	public function _data()
	{
		$where = "`user_to` = {$this->id} AND `read` = 0";
		$sql = "SELECT `type`,`img`,`title`,`content`,`found_date`,`code` FROM  `user_message` WHERE {$where} ORDER BY `id` DESC;";
		$result = $this->conn->query($sql);
		$nd = $cd = [];
		$nc = $cc = 0;
		$sum = 0;
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$type = $row['type'];
				$sum += 1;
				switch ($type) {
					case '0':
						$nd[] = $row;
						$nc += 1;
						break;
					case '1':
						$cd[] = $row;
						$cc += 1;
						break;
				}
			}
		}
		$notice = ['data' => $nd, 'count' => $nc];
		$chat = ['data' => $cd, 'count' => $cc];
		$this->res('调试成功', 1, ['notice' => $notice, 'chat' => $chat, 'sum' => $sum]);
	}

	public function _getPastTime($date, $type = 0)
	{
		$end = date("Y-m-d H:i:s");
		$days = floor((strtotime($end) - strtotime($date)) / 86400);
		$arr = [
			[0, '今天'],
			[1, '昨天'],
			[2, '2天前'],
			[3, '3天前'],
			[4, '4天前'],
			[5, '5天前'],
			[6, '6天前'],
			[7, '1周前'],
			[14, '2周前'],
			[21, '3周前'],
			[28, '4周前'],
			[30, '1个月前'],
			[32, date('Y年m月d日 H:i:s', strtotime($date))]
		];
		foreach ($arr as $key) {
			if ($days >= $key[0]) {
				$text = $key[1];
			}
		}
		return $text;
	}

	//将通知消息设置为已读
	public function _read()
	{
		$code = $this->is('code', '');
		$sql = "UPDATE `user_message` SET `read` = 1 WHERE `code` = '{$code}' AND `user_to` = {$this->id};";
		$this->run($sql, false);
	}

	//清空通知
	public function _clearMsg()
	{
		$sql = "UPDATE `user_message` SET `read` = 1 WHERE `user_to` = {$this->id} AND `type` = 0;";
		$this->run($sql, false);
	}

	//清空私信
	public function _clearChat()
	{
		$sql = "UPDATE `user_message` SET `read` = 1 WHERE `user_to` = {$this->id} AND `type` = 1;";
		$this->run($sql, false);
	}
}
$web = new _web(2, 'id');
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>消息</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
	<style>
		body {
			padding: 0;
			overflow: hidden;
		}

		.layui-tab-title {
			text-align: center;
		}

		.layui-tab-content {
			padding: 0;
		}

		.notice,
		.chat {
			padding: 5px 0px;
			height: 300px;
			overflow: auto;
		}

		.notice>li,
		.chat>li {
			height: 60px;
			display: flex;
			border-bottom: 1px solid #e8e8e8;
			cursor: pointer;
		}

		.notice>li:hover,
		.chat>li:hover {
			background-color: rgba(0, 0, 0, 0.05);
		}

		.notice-icon {
			width: 60px;
			height: 60px;
			display: flex;
		}

		.notice-icon>img {
			width: 32px;
			height: 32px;
			border-radius: 100%;
			margin: auto;
		}

		.notice-body {
			width: calc(100% - 60px);
			height: 100%;
		}

		.notice-title {
			font-size: 14px;
			color: #666666;
			margin-top: 10px;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
		}

		.notice-time {
			font-size: 12px;
			color: #b2b2b2;
			margin-top: 5px;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
		}

		.not::before {
			content: "";
			width: 80px;
			height: 80px;
			display: block;
			margin: 40px auto 20px auto;
			background-image: url(../images/notice-not.png);
			background-repeat: no-repeat;
			background-size: 100% auto;
		}

		.not::after {
			content: "暂无通知";
			display: block;
			text-align: center;
			color: #c2c2c2;
		}

		.chat .not::after {
			content: "暂无私信";
		}

		.chat .not::before {
			background-image: url(../images/chat-not.png);
		}

		.footer {
			position: absolute;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #FFFFFF;
			height: 42px;
			display: flex;
			border-top: 1px solid #ededed;
			line-height: 42px;
		}

		.footer>button {
			background-color: transparent;
			border: none;
			width: 49.9%;
			color: #606266;
			cursor: pointer;
		}

		.footer>button:hover {
			background-color: rgba(0, 0, 0, 0.05);
		}

		.footer>.layui-table-divide {
			height: 15px;
			margin-left: 0px;
			margin-right: 0px;
			margin-top: 15px;
		}
	</style>
</head>

<body class="layui-form">
	<div class="layui-tab layui-tab-brief">
		<ul class="layui-tab-title">
			<li class="layui-this">通知</li>
			<li>私信</li>
			<li>待办</li>
		</ul>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
				<div class="notice">

				</div>
				<div class="footer">
					<button class="clear-msg">清空通知</button>
					<span class="layui-table-divide"></span>
					<button class="refresh">刷新通知</button>
				</div>
			</div>
			<div class="layui-tab-item">
				<div class="chat">
				</div>
				<div class="footer">
					<button class="clear-chat">清空私信</button>
					<span class="layui-table-divide"></span>
					<button class="refresh">刷新私信</button>
				</div>
			</div>
			<div class="layui-tab-item">
				<div class="not"></div>
			</div>
		</div>
	</div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
	$(document).on("click", ".notice>li", function() {
		noticeRead(this);
	});
	$(document).on('click', '.clear-msg', function() {
		clearMsg();
	});
	$(document).on('click', '.clear-chat', function() {
		clearChat();
	});
	$(document).on('click', '.chat>li', function() {
		chatRead(this);
	});
	$('.refresh').click(function() {
		init();
	});

	function noticeRead(elem) {
		$.ajax({
			url: api.url('read'),
			type: 'POST',
			dataType: 'json',
			data: {
				code: $(elem).attr('code')
			},
			success: function(data) {
				var title = $(elem).find('.notice-title').text();
				var content = $(elem).attr('content');
				parent.layer.open({
					type: 1,
					title: title,
					area: ["700px", "600px"],
					maxmin: false,
					content: '<div style="padding: 15px;">' + content + '</div>',
					shade: 0.3
				});
				if (data.code == 1) {
					init();
				}
			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
	}

	function clearMsg() {
		$.ajax({
			url: api.url('clearMsg'),
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				layer.msg("正在执行", {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: function(data) {
				layer.msg(data.msg, {
					icon: data.code,
					time: 1000
				}, function() {
					if (data.code == 1) {
						init();
					}
				});

			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
	}

	function clearChat() {
		$.ajax({
			url: api.url('clearChat'),
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				layer.msg("正在执行", {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: function(data) {
				layer.msg(data.msg, {
					icon: data.code,
					time: 1000
				}, function() {
					if (data.code == 1) {
						init();
					}
				});

			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
	}

	function chatRead(elem) {
		$.ajax({
			url: api.url('read'),
			type: 'POST',
			dataType: 'json',
			data: {
				code: $(elem).attr('code')
			},
			success: function(data) {
				var title = $(elem).find('.notice-title').text();
				var user_to = $(elem).attr('content');
				parent.layer.open({
					type: 2,
					title: '聊天会话',
					content: 'page/chat_msg.php?user_to=' + user_to,
					area: ['600px', '550px'],
					anim: 5,
					maxmin: false,
					shadeClose: true,
					success: function() {
						//var index = parent.layer.getFrameIndex(window.name);
						//parent.layer.close(index);
					}
					//scrollbar: false
				});
				if (data.code == 1) {
					init();
				}
			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
	}

	window.init = function() {
		$.ajax({
			url: api.url('data'),
			type: 'POST',
			dataType: 'json',
			success: function(data) {

				if (data.code == 1) {
					//通知
					var elem = $('.notice');
					var li = $('.layui-tab-title>li').eq(0);
					elem.html('');
					for (var key in data.data.notice.data) {
						var json = data.data.notice.data[key];
						var img = json.img != '' ? json.img : '../images/picture.png';
						var item = `<li code="${json.code}" content="${json.content}"><div class="notice-icon"><img src="${img}" /></div><div class="notice-body"><div class="notice-title">${json.title}</div><div class="notice-time">${json.found_date}</div></div></li>`;
						elem.append(item);
					}
					li.html(`通知(${data.data.notice.count})`);
					if (data.data.notice.count == 0) {
						elem.html('<div class="not"></div>');
						li.html('通知');
					}
					//私信
					var elem = $('.chat');
					var li = $('.layui-tab-title>li').eq(1);
					elem.html('');
					for (var key in data.data.chat.data) {
						var json = data.data.chat.data[key];
						var item = `<li code="${json.code}" content="${json.content}"><div class="notice-icon"><img src="${json.img}" /></div><div class="notice-body"><div class="notice-title">${json.title}</div><div class="notice-time">${json.found_date}</div></div></li>`;
						elem.append(item);
					}
					li.html(`私信(${data.data.chat.count})`);
					if (data.data.chat.count == 0) {
						elem.html('<div class="not"></div>');
						li.html('私信');
					}
					var dot = parent.$(".layui-badge.message-dot");
					dot.html(data.data.sum);
					if (data.data.sum == 0) {
						dot.addClass('layui-hide');
					}
				} else {
					layer.msg(data.msg, {
						icon: data.code
					});
				}
			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
	};
	init();
</script>

</html>