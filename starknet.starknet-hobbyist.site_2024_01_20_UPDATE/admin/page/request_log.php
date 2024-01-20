<?php
include '../php/api.php';
class web extends _api
{
	public function _data()
	{
		$u = $this->is('username');
		$t = $this->is('type');
		$i = $this->is('ip');
		$f = "`url`,`found_date`,`type`,`ip`,`get`,`post`,
		(SELECT `username` FROM `user_data` WHERE user_data.id = request_log.user_id limit 1) AS `username`";
		$w = '';
		if ($u) {
			$sql = "SELECT `id` FROM `user_data` WHERE `username` = '{$u}' limit 1;";
			$res = $this->run($sql);
			if ($res->num_rows > 0) {
				$row = $res->fetch_assoc();
				$w .= $w ? " AND `user_id` = '{$row['id']}'" : "`user_id` = '{$row['id']}'";
			} else {
				$w .= $w ? " AND `user_id` = '-1'" : "`user_id` = '-1'";
			}
		}
		if ($t) $w .= $w ? " AND `type` = '{$t}'" : "`type` = '{$t}'";
		if ($i) $w .= $w ? " AND `ip` = '{$i}'" : "`ip` = '{$i}'";
		if ($w) $w = " WHERE {$w}";
		$w .= " ORDER BY `id` DESC";
		$this->query('request_log', $w, $f);
	}

	public function _clear()
	{
		$sql = "truncate table `request_log`";
		$this->run($sql, false);
	}
};
$web = new web(2, 'id');
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>页面请求日志</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
	<style>
		.get {
			color: #41ca9d;
		}

		.post {
			color: #ed8936;
		}
	</style>
</head>

<body class="layui-form">
	<div class="layui-row layui-col-space15">
		<div class="layui-col-md12">
			<div class="layui-card">
				<div class="layui-card-body">
					<div class="layui-msg">
						<i class="layui-icon layui-icon-tips"></i>
						<p>温馨提示：页面请求日志，记录了所有页面的请求信息，如发现异常请求，请及时处理。</p>
					</div>
				</div>
			</div>
		</div>
		<div class="layui-col-md12">
			<div class="layui-card">
				<div class="layui-card-body">
					<div class="layui-form-item">
						<label class="layui-form-label">查询条件</label>
						<div class="layui-input-inline">
							<select name="fieldTypeSelect" lay-verify="required" lay-filter="fieldTypeSelect" lay-search>
								<option value=""></option>
								<option value="username">用户名</option>
								<option value="found_date">访问时间</option>
								<option value="type">访问方式</option>
								<option value="ip">来源IP</option>
							</select>
						</div>
						<div class="layui-input-inline" name="fieldTypeInput">
							<div class="showSearch usernameItem layui-hide">
								<input type="text" name="username" class="layui-input" placeholder="请输入用户名" />
							</div>
							<div class="showSearch found_dateItem layui-hide">
								<input type="text" name="found_date" class="layui-input" placeholder="选择时间" lay-type="date" />
							</div>
							<div class="showSearch typeItem layui-hide">
								<select name="type" lay-verify="required" lay-filter="type" lay-search>
									<option value="">全部</option>
									<option value="get">GET</option>
									<option value="post">POST</option>
								</select>
							</div>
							<div class="showSearch ipItem layui-hide">
								<input type="text" name="ip" class="layui-input" placeholder="请输入来源IP" />
							</div>
						</div>
						<button class="layui-btn layui-btn-sm layui-btn-normal search">
							<i class="layui-icon layui-icon-search"></i>
							<span>查询</span>
						</button>
						<button class="layui-btn layui-btn-sm layui-btn-primary resetting">
							<i class="layui-icon layui-icon-refresh"></i>
							<span>重置条件</span>
						</button>
						<button class="layui-btn layui-btn-sm layui-btn-plug-danger clear">
							<i class="layui-icon layui-icon-delete"></i>
							<span>清空记录</span>
						</button>
					</div>
					<table id="request_log" lay-filter="request_log"></table>
				</div>
			</div>
		</div>
	</div>

</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
	var $ = layui.$,
		table = layui.table,
		form = layui.form,
		upload = layui.upload,
		laydate = layui.laydate;
	table.render({
		elem: "#request_log",
		url: api.url('data'),
		page: true,
		title: "页面请求日志",
		skin: "line",
		where: where(),
		cols: [
			[{
				field: 'username',
				title: '操作用户',
				width: 150,
				align: 'center'
			}, {
				field: 'url',
				title: '访问链接',
				minWidth: 300
			}, {
				field: 'type',
				title: '访问方式',
				width: 120,
				align: 'center',
				templet: function(d) {
					if (d.type == 'GET') return '<span class="get">GET</span>';
					if (d.type == 'POST') return '<span class="post">POST</span>';
					return '<span class="other">其他</span>';
				}
			}, {
				field: 'get',
				title: 'GET参数',
				width: 300
			}, {
				field: 'post',
				title: 'POST参数',
				width: 300,
				templet: d => {
					return d.post == '[]' ? '-' : d.post;
				}
			}, {
				field: 'ip',
				title: '来源IP',
				width: 200
			}, {
				field: 'found_date',
				title: '访问时间',
				width: 200
			}]
		]
	});
	laydate.render({
		elem: '[name=found_date]',
		type: 'date',
		change: function(value, date, endDate) {
			setTimeout(function() {
				reload('request_log');
			}, 100);
		},
		done: function(value, date, endDate) {
			setTimeout(function() {
				reload('request_log');
			}, 100);
		}
	});
	form.on('select(type)', function(data) {
		reload('request_log');
	});
	$(document).on('click', '.clear', function() {
		layer.confirm('确定清空所有请求记录吗？', (index) => {
			clear(index);
		});
	});

	function clear(index) {
		$.ajax({
			url: api.url('clear'),
			type: 'POST',
			dataType: 'json',
			beforeSend: () => {
				layer.msg('正在加载', {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: r => {
				layer.close(index);
				layer.msg(r.msg, {
					icon: r.code
				});
				if (r.code == 1) reload();
			},
			error: (r) => layer.alert(r.responseText, {
				icon: 2
			})
		});
	}
</script>

</html>