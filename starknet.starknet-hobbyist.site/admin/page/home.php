<?php
include '../php/api.php';
class _web extends _api
{


	//获取头像
	public function _picture()
	{
		return $this->user['picture'];
	}

	//获取今天是星期几
	public function _getDayOfWeek()
	{
		$days = ['一', '二', '三', '四', '五', '六', '日'];
		return '星期' . $days[date('N', strtotime(date('Y-m-d'))) - 1];
	}

	//欢迎语
	public function _welcome()
	{
		$h = date('H');
		if ($h >= 0 && $h < 7) {
			return "天还没亮，起的太早了，要注意身体哦！ ";
		}
		if ($h >= 7 && $h < 12) {
			return "上午好！开始您一天的工作吧！";
		}
		if ($h >= 12 && $h < 14) {
			return "中午好！午休时间哦！";
		}
		if ($h >= 14 && $h < 18) {
			return "下午茶的时间到了，休息一下吧！ ";
		}
		if ($h >= 18 && $h < 22) {
			return "还在加班呢，休息一下吧！ ";
		}
		if ($h >= 22 && $h < 24) {
			return "很晚了哦，注意休息呀！";
		}
	}

	//获取天气数据
	public function _tianqi()
	{
		$url = "http://www.yiketianqi.com/api?version=v9&appid=23035354&appsecret=8YvlPNrz&ip=" . $this->ip;
		$res = $this->curl($url);
		$city = isset($res['city']) ? $res['city'] : [];
		$i = isset($res['data'][0]['tem']) ? $res['data'][0]['tem'] : '';
		$x = isset($res['data'][0]['tem1']) ? $res['data'][0]['tem1'] : '';
		$w = isset($res['data'][0]['wea']) ? $res['data'][0]['wea'] : '';
		$t = isset($res['data'][0]['air_tips']) ? $res['data'][0]['air_tips'] : '';
		return "{$city}今日{$w}，{$i}℃ - {$x}℃，{$t}";
	}

	//获取消息总数
	public function _getMsgCount()
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `user_message` WHERE `user_to` = {$this->id} AND `read` = 0;";
		$res = $this->run($sql);
		if ($res->num_rows > 0) {
			$row = $res->fetch_assoc();
			return number_format(intval($row['count']), 0);
		}
		return number_format(0, 0);
	}

	//获取卡片数据
	public function _card()
	{
		$data = [];
		$sql = "SELECT `id`,`name`,`icon`,`sql`,`color`,`contrast`,`sql1`,`url` FROM  `home_card` ORDER BY `indexs`,`id` ASC;";
		$res = $this->run($sql);
		if ($res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				$sql = $this->getInSql($row['sql']);
				$res1 = $this->run($sql);
				$r = $res1->fetch_assoc();
				$a = explode(' ', $sql);
				$item = [
					'id' => $row['id'],
					'name' => $row['name'],
					'icon' => $row['icon'],
					'color' => $row['color'],
					'value' => floatval(strtolower($a[2]) == 'as' ? $r[$a[3]] : $r[$a[1]]),
					'contrast' => $row['contrast'],
					'prev' => $this->_homePrev($row['contrast'], $row['sql1']),
					'url' => $row['url']
				];
				$data[] = $item;
			}
		}
		$this->res('调试成功', 1, $data);
	}

	/**
	 * 获取字符串内容(会将{}内容替换为变量)
	 * @param string $str 字符串
	 * @return string
	 */
	public function getInSql($str)
	{
		$is = preg_match_all('/\{.+?\}/', $str, $arr);
		if (!$is) return $str;
		foreach ($arr[0] as $key) {
			$v = $this->getVal($key);
			$str = str_replace($key, $v, $str);
		}
		return $str;
	}

	/**
	 * 获取变量值
	 * @param string $v 变量
	 * @return mixed
	 */
	public function getVal($v)
	{
		$v = substr($v, 1);
		$v = substr($v, 0, strlen($v) - 1);
		$n = eval("return {$v};");
		return $n;
	}

	public function _homePrev($s, $m)
	{
		if ($s == '0') {
			return false;
		}
		$sql = $this->getInSql($m);
		$res = $this->run($sql);
		$r = $res->fetch_assoc();
		$a = explode(' ', $sql);
		return floatval(strtolower($a[2]) == 'as' ? $r[$a[3]] : $r[$a[1]]);
	}

	public function _card_del()
	{
		$this->form([
			'id' => ['required', 'id']
		]);
		$id = $_REQUEST["id"];
		$sql = "SELECT `icon` FROM  `home_card` WHERE `id` = '{$id}';";
		$result = $this->run($sql);
		if ($result->num_rows == 0) $this->res('卡片不存在', 3);
		$row = $result->fetch_assoc();
		$this->delFile($row['icon']);
		$sql = "DELETE FROM `home_card` WHERE `id` = {$id};";
		$this->run($sql, false);
	}

	public function _card_copy()
	{
		$this->copy('home_card');
	}

	/**
	 * 复制数据
	 * @param string $s 表名
	 * @param int $id ID
	 * @param bool $r 是否返回SQL语句(非必填，默认false)
	 * @return string|void
	 */
	public function copy($s, $id = false, $r = false)
	{
		$id = !$id ? $this->is('id', 0) : $id;
		$sql = "SELECT * FROM  `{$s}` WHERE `id` = '{$id}' limit 1";
		$res = $this->run($sql);
		if ($res->num_rows == 0) $this->res('ID不存在', 3);
		$row = $res->fetch_assoc();
		$f = $fv = '';
		foreach ($row as $k => $v) {
			$v = addslashes($v);
			$f .= $k != 'id' ? "`{$k}`," : '';
			$fv .= $k != 'id' ? "'{$v}'," : '';
		}
		$f = substr($f, 0, strlen($f) - 1);
		$fv = substr($fv, 0, strlen($fv) - 1);
		$sql = "INSERT INTO `{$s}` ({$f}) VALUES ({$fv});";
		if ($r) return $sql;
		$this->run($sql, false);
	}

	//排序选项卡
	public function _SortCard()
	{
		$d = $this->is('data', []);
		$s = 0;
		foreach ($d as $k) {
			$id = $k['id'];
			$i = $k['indexs'];
			$sql = "UPDATE `home_card` SET `indexs` = {$i} WHERE `id` = {$id};";
			$res = $this->run($sql);
			if ($res) $s += 1;
		}
		$this->res($s > 0 ? '排序成功' : '排序失败', $s > 0 ? 1 : 3);
	}

	//获取用户链接
	public function _Action()
	{
		$d = [];
		$sql = "SELECT `id`,`name`,`url` FROM  `user_action` WHERE `user_id` = {$this->id} ORDER BY `indexs`,`id` ASC;";
		$res = $this->run($sql);
		if ($res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				$d[] = $row;
			}
		}
		$this->res('调试成功', 1, $d);
	}

	//删除用户链接
	public function _action_del()
	{
		$this->form([
			'id' => ['required', 'id']
		]);
		$id = $_REQUEST["id"];
		$sql = "DELETE FROM `user_action` WHERE `id` = {$id} AND `user_id` = {$this->id};";
		$this->run($sql, false);
	}

	//排序网页链接
	public function _SortAction()
	{
		$d = $this->is('data', []);
		$s = 0;
		foreach ($d as $k) {
			$id = $k['id'];
			$i = $k['indexs'];
			$sql = "UPDATE `user_action` SET `indexs` = {$i} WHERE `id` = {$id};";
			$res = $this->run($sql);
			if ($res) $s += 1;
		}
		$this->res($s > 0 ? '排序成功' : '排序失败', $s > 0 ? 1 : 3);
	}
}
if (isset($_REQUEST['method'])) {
	$method = $_REQUEST['method'];
	switch ($method) {
		case 'card':
			$web = new _web(2);
			$web->_card();
			break;
		case 'card_del':
			$web = new _web(2, 'id', false, true);
			$web->_card_del();
			break;
		case 'card_copy':
			$web = new _web(2, 'id', false, true);
			$web->_card_copy();
			break;
		case 'SortCard':
			$web = new _web(2, 'id', false, true);
			$web->_SortCard();
			break;
		case 'Action':
			$web = new _web(2);
			$web->_Action();
			break;
		case 'action_del':
			$web = new _web(2);
			$web->_action_del();
			break;
		case 'SortAction':
			$web = new _web(2);
			$web->_SortAction();
			break;
		default:
			$web = new _api();
			$web->res('方法不存在', 3);
			break;
	}
} else {
	$web = new _web(2, 'id,picture');
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta name="renderer" content="webkit">
	<meta charset="utf-8">
	<title>首页</title>
	<link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>">
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/home.css?v=<?php echo $web->v; ?>" />
</head>

<body>
	<!-- 卡片组 -->
	<div class="layui-row layui-col-space15">
		<div class="layui-col-md8">
			<div class="layui-row layui-col-space15">
				<div class="layui-col-md12">
					<div class="layui-card">
						<div class="layui-card-body" style="padding: 20px;">
							<div class="layui-row">
								<div class="layui-col-md8">
									<img src="<?php echo $web->_picture(); ?>" class="picture">
									<div class="layui-inline">
										<span class="title"><?php echo $web->_welcome(); ?></span>
										<span class="weather layui-hide-xs"><?php echo $web->_tianqi(); ?></span>
									</div>
								</div>
								<div class="layui-col-md4">
									<div class="workplace">
										<div class="workplace-header">
											<span><i class="layui-icon layui-icon-list"></i>项目数</span>
										</div>
										<div class="workplace-content">0</div>
									</div>
									<div class="workplace">
										<div class="workplace-header">
											<span><i class="layui-icon layui-icon-date date"></i>待办项</span>
										</div>
										<div class="workplace-content">0</div>
									</div>
									<div class="workplace">
										<div class="workplace-header">
											<span><i class="layui-icon layui-icon-notice notice"></i>消息</span>
										</div>
										<div class="workplace-content">
											<?php echo $web->_getMsgCount(); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="layui-col-md12">
					<div class="layui-row layui-col-space15 card-list"></div>
				</div>
			</div>
		</div>
		<div class="layui-col-md4">
			<div class="layui-row layui-col-space15">
				<div class="layui-col-md12">
					<div class="layui-card">
						<div class="layui-card-body" style="padding: 15px;">
							<div style="height: 78px">
								<div class="action"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/Sortable.min.js?v=<?php echo $web->v; ?>"></script>
<script>
	var admin = '<?php echo $web->admin == true ? "true" : "false"; ?>',
		Time = false,
		format = num => {
			return (num + '').replace(/(\d{1,3})(?=(\d{3})+(?:$|\.))/g, '$1,');
		},
		card = {
			init() {
				let g = () => {
					Time && clearInterval(Time);
					$.ajax({
						url: api.url('card'),
						type: 'GET',
						dataType: 'json',
						success: function(r) {
							if (r.code != 1) return layer.msg(r.msg, {
								icon: r.code
							});
							let e = $('.card-list'),
								n = e.find('.card'),
								f = (j) => {
									if (j.contrast == '0') return '';
									let v = j.value,
										p = j.prev;
									if (v == p) return '<span class="data-equality">持平0.00</span>';
									if (v > p) return `<span class="data-up">上升${(v - p)}</span>`;
									if (v < p) return `<span class="data-down">下降${(p - v)}</span>`;
								},
								s = (n, v) => {
									let a = Number(n.parents('.card').attr('data-num')),
										t = this;
									if (a == v) return;
									if (v > a) {
										let i = a;
										n.text(format(i));
										let s = setInterval(() => {
											i++;
											n.text(format(i));
											if (i >= v) clearInterval(s);
										}, 10);
									} else {
										let i = a;
										n.text(format(i));
										let s = setInterval(() => {
											i--;
											n.text(format(i));
											if (i <= v) clearInterval(s);
										}, 10);
									}
									n.parents('.card').attr('data-num', v);
								};
							if (r.data.length == 0) {
								let v = admin == 'true' ? '点击新增卡片' : '什么也没有';
								e.html('<div class="card-not">' + v + '</div>');
								return;
							}
							if (!n.length) {
								e.empty();
								r.data.forEach((v, i) => {
									let c = f(v);
									let n = `<div class="layui-col-xs6 layui-col-sm6 layui-col-md6 layui-col-lg3">
				<a class="card" data-id="${v.id}" href="${v.url != '' ? v.url : 'javascript:;'}" style="background-color:${v.color}" data-num="${v.value}">
					<div class="card-icon">
						<div class="card-icon-bor">
							<img src="${v.icon}" style="filter: drop-shadow(30px 30px #ffffff)"/>
						</div>
					</div>
					<div class="card-box">
						<div class="card-box-title">${v.name}</div>
						<div class="card-box-num">${format(v.value)}</div>
						<div class="card-yesterday">
							<div class="card-yesterday-html">${c}</div>
						</div>
					</div>
				</a>
			</div>`;
									e.append(n);
								});
							} else {
								r.data.forEach((v, i) => {
									let c = f(v);
									s(n.eq(i).find('.card-box-num'), v.value);
									n.eq(i).find('.card-yesterday-html').html(c);
								});
							}
							setTimeout(() => g(), 3000);
						},
						error: r => layer.alert(r.responseText, {
							icon: 2
						})
					});
				};
				this.get = g;
				g();
			}
		};
	class _Admin {
		constructor() {
			var self = this;
			api.menu('.card-list .card', function(el, type) {
				layui.dropdown.render({
					elem: el,
					show: true,
					trigger: type,
					data: [{
							title: '新增卡片',
							id: 'add'
						}, {
							title: '修改卡片',
							id: 'edit'
						},
						{
							title: '复制卡片',
							id: 'copy',
						},
						{
							title: '删除卡片',
							id: 'del'
						}
					],
					click: function(res, e) {
						if (res.id == 'add') {
							self.add();
						}
						if (res.id == 'edit') {
							self.add(this.elem.attr('data-id'));
						}
						if (res.id == 'copy') {
							self.copy(this.elem.attr('data-id'));
						}
						if (res.id == 'del') {
							self.del(this.elem.attr('data-id'));
						}
					},
					style: 'box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);'
				});
			});

			$(document).on('click', '.card-not', function() {
				if (admin == 'false') {
					return false;
				}
				self.add();
			});
			new Sortable($(".card-list")[0], {
				//handle: '.sort',
				animation: 150,
				ghostClass: 'blue-background-class',
				onStart: function() {
					Time && clearInterval(Time);
				},
				onEnd: function() {
					var data = [];
					$('.card').each(function(index) {
						var json = {
							id: $(this).attr('data-id'),
							indexs: index
						};
						data.push(json);
					})
					self.sort(data);
				}
			});
			$(document).on('click', '.card', function(e) {
				e.preventDefault();
				var url = $(this).attr('href');
				var title = $(this).find('.card-box-title').text();
				parent.App.add(url, title, '首页');
			});
		}

		add(id = "") {
			layer.open({
				type: 2,
				title: (id == "" ? "新增" : "修改") + "卡片",
				area: ["820px", "600px"],
				maxmin: false,
				content: "set_card.php?id=" + id,
				shade: 0.3
			});
		}

		del(id) {
			layer.confirm('确定删除此卡片吗？', function(index) {
				$.ajax({
					url: api.url('card_del'),
					type: 'POST',
					dataType: 'json',
					data: {
						id: id
					},
					beforeSend: function() {
						layer.msg("正在执行", {
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
							layer.close(index);
							card();
						}
					},
					error: r => layer.alert(r.responseText, {
						icon: 2
					})
				});
			});
		}

		copy(id) {
			$.ajax({
				url: api.url('card_copy'),
				type: 'POST',
				dataType: 'json',
				data: {
					id: id
				},
				beforeSend: function() {
					layer.msg("正在执行", {
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
						card();
					}
				},
				error: r => layer.alert(r.responseText, {
					icon: 2
				})
			});
		}

		sort(data) {
			$.ajax({
				url: api.url('SortCard'),
				type: 'POST',
				dataType: 'json',
				data: {
					data: data,
					surface: 'home_card'
				},
				success: function(data) {
					if (data.code == 1) {
						card();
					}
				},
				error: r => layer.alert(r.responseText, {
					icon: 2
				})
			});
		}
	};
	class _Action {
		constructor() {
			var self = this;
			this.init();
			$(document).on('click', '.add-action', function() {
				self.add();
			});
			api.menu('.workplace-action', function(el, type) {
				layui.dropdown.render({
					elem: el,
					show: true,
					trigger: type,
					data: [{
							title: '新增链接',
							id: 'add'
						}, {
							title: '修改链接',
							id: 'edit'
						},
						{
							title: '删除链接',
							id: 'del'
						}
					],
					click: function(res, e) {
						if (res.id == 'add') {
							var len = $('.workplace-action').length;
							console.log(len);
							if (len >= 8) {
								layer.msg('最多只能添加8个链接', {
									icon: 3
								});
								return false;
							}
							self.add();
						}
						if (res.id == 'edit') {
							self.add(this.elem.attr('data-id'));
						}
						if (res.id == 'copy') {
							self.copy(this.elem.attr('data-id'));
						}
						if (res.id == 'del') {
							self.del(this.elem.attr('data-id'));
						}
					},
					style: 'box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);'
				});
			});
			new Sortable($(".action")[0], {
				animation: 150,
				ghostClass: 'blue-background-class',
				onStart: function() {
					Time && clearInterval(Time);
				},
				onEnd: function() {
					var data = [];
					$('.workplace-action').each(function(index) {
						var json = {
							id: $(this).attr('data-id'),
							indexs: index
						};
						data.push(json);
					})
					self.sort(data);
				}
			});
		}

		init() {
			$.ajax({
				url: api.url('Action'),
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if (data.code == 1) {
						var elem = $(".action");
						elem.html('');
						for (var key in data.data) {
							var json = data.data[key];
							var item = `<a href="${json.url}" class="workplace-action" target="_blank" data-id="${json.id}" title="${json.name}(${json.url})">${json.name}</a>`;
							elem.append(item);
						}
						if (data.data.length == 0) {
							elem.html('<button class="layui-btn layui-btn-sm layui-btn-normal add-action"> <i class="layui-icon layui-icon-add-1"></i> <span>添加自定义链接</span> </button>');
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
		}

		add(id = "") {
			layer.open({
				type: 2,
				title: (id == "" ? "新增" : "修改") + "网页",
				area: ["500px", "250px"],
				maxmin: false,
				content: "set_action.php?id=" + id,
				shade: 0.3
			});
		}

		del(id) {
			var self = this;
			layer.confirm('确定删除此网页链接吗？', function(index) {
				$.ajax({
					url: api.url('action_del'),
					type: 'POST',
					dataType: 'json',
					data: {
						id: id
					},
					beforeSend: function() {
						layer.msg("正在执行", {
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
							layer.close(index);
							self.init();
						}
					},
					error: r => layer.alert(r.responseText, {
						icon: 2
					})
				});
			});
		}

		sort(data) {
			$.ajax({
				url: api.url('SortAction'),
				type: 'POST',
				dataType: 'json',
				data: {
					data: data
				},
				success: function(data) {},
				error: r => layer.alert(r.responseText, {
					icon: 2
				})
			});
		}
	};
	card.init();
	new _Action();
	if (admin == 'true') new _Admin();
</script>

</html>