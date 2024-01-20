<?php
class _web extends _api
{
	public function _menu()
	{
		$sql = "SELECT `id`,`state`,`name`,`icon`,`juris` FROM  `menu_list` ORDER BY `indexs`,`id` ASC;";
		$res = $this->run($sql);
		$html = '';
		if ($res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				$node = $this->_node($row);
				$show = $row['state'] == '1' ? 'layui-nav-itemed' : '';
				if ($this->admin || $this->sys['juris_state'] == 0) {
					$html .= "<li class='layui-nav-item {$show}' data-id='{$row['id']}' state='{$row['state']}'><a href='javascript:;'><i class='{$row['icon']}'></i>{$row['name']}<span class='lay-sort'></span></a><dl class='layui-nav-child'>{$node}</dl></li>";
				} else {
					if ($row['juris'] == '0') {
						$html .= "<li class='layui-nav-item {$show}' data-id='{$row['id']}' state='{$row['state']}'><a href='javascript:;'><i class='{$row['icon']}'></i>{$row['name']}</a><dl class='layui-nav-child'>{$node}</dl></li>";
					} else {
						$f = $_SERVER['PHP_SELF'];
						$t = $row['name'];
						$q = "SELECT `id` FROM  `juris_data` WHERE `path` = '{$f}' AND `method` = '{$t}' limit 1;";
						$r = $this->run($q);
						if ($r->num_rows > 0) {
							$rw = $r->fetch_assoc();
							$q = "SELECT `id` FROM  `juris_list` WHERE `roles_id` = '{$this->user['roles_id']}' AND `juris_id` = '{$rw['id']}' limit 1;";
							$r = $this->run($q);
							if ($r->num_rows > 0) {
								$html .= "<li class='layui-nav-item {$show}' data-id='{$row['id']}' state='{$row['state']}'><a href='javascript:;'><i class='{$row['icon']}'></i>{$row['name']}</a><dl class='layui-nav-child'>{$node}</dl></li>";
							}
						}
					}
				}
			}
		}
		if ($html == '' && $this->admin) {
			$html = '<li class="add-meun" style="text-align: center;line-height: 40px;cursor: pointer;">新增菜单</li>';
		}
		return $html;
	}

	public function _node($d)
	{
		$sql = "SELECT `id`,`name`,`url`,`juris` FROM  `menu_node` WhERE `menu_id` = {$d['id']} ORDER BY `indexs`,`id` ASC;";
		$res = $this->run($sql);
		$html = "";
		if ($res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				if ($this->admin || $this->sys['juris_state'] == 0) {
					$html .= "<dd><a href='{$row['url']}?v={$this->v}' data-id='{$row['id']}'>{$row['name']}<span class='lay-sort'></span></a></dd>";
				} else {
					if ($row['juris'] == '0') {
						$html .= "<dd><a href='{$row['url']}?v={$this->v}' data-id='{$row['id']}'>{$row['name']}</a></dd>";
					} else {
						$f = $_SERVER['PHP_SELF'];
						$t = $row['name'];
						$q = "SELECT `id` FROM  `juris_data` WHERE `path` = '{$f}' AND `method` = '{$t}' limit 1;";
						$r = $this->run($q);
						if ($r->num_rows > 0) {
							$rw = $r->fetch_assoc();
							$q = "SELECT `id` FROM  `juris_list` WHERE `roles_id` = '{$this->user['roles_id']}' AND `juris_id` = '{$rw['id']}' limit 1;";
							$r = $this->run($q);
							if ($r->num_rows > 0) {
								$html .= "<dd><a href='{$row['url']}?v={$this->v}' data-id='{$row['id']}'>{$row['name']}</a></dd>";
							}
						}
					}
				}
			}
		}
		if ($html == '' && $this->admin) {
			$html = '<li class="add-item" style="text-align: center;line-height: 40px;cursor: pointer;">新增项目</li>';
		}
		return $html;
	}

	public function _color()
	{
		if ($this->user['time_state'] == '1') {
			$date = date('md');
			switch ($date) {
				case '1213':
					$c =  'filter: grayscale();';
					break;
				default:
					$c = '';
					break;
			}
			return $c;
		}
		return $this->user['special_color'] != '' ? $this->user['special_color'] : '';
	}
}
$web = new _web(2, "*");
?>
<!DOCTYPE html>
<html style="<?php echo $web->_color(); ?>">

<head>
	<meta charset="utf-8">
	<title><?php echo $web->sys['title']; ?></title>
	<meta name="renderer" content="webkit" />
	<meta name="Keywords" content="<?php echo $web->sys['Keywords']; ?>" />
	<?php echo $web->user['is_mobile'] == '1' ? '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />' : ''; ?>
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="css/admin.css?v=<?php echo $web->v; ?>" />
</head>

<body theme="<?php echo $web->user['theme']; ?>" show-footer="<?php echo $web->user['show_footer']; ?>" tab-type="<?php echo $web->user['tab_type']; ?>" select-type="<?php echo $web->user['select_type']; ?>">
	<div class="layui-layout layui-layout-admin">
		<!-- 顶部 -->
		<div class="layui-header">
			<ul class="layui-nav layui-layout-left">
				<li class="layui-nav-item">
					<i class="layui-icon layui-icon-shrink-right" lay-header-event="menuLeft"></i>
				</li>
				<li class="layui-nav-item" lay-unselect="">
					<a href="javascript:;" class="refresh-this" title="刷新当前页面">
						<i class="layui-icon layui-icon-refresh"></i>
					</a>
				</li>
				<span class="layui-breadcrumb layui-hide-xs">
					<a href="javascript:;"></a>
					<a href="javascript:;"></a>
				</span>
			</ul>
			<ul class="layui-nav layui-layout-right" style="text-align: center;">
				<li class="layui-nav-item" lay-unselect="">
					<a href="javascript:;" class="message<?php echo $web->user['show_msg'] == '0' ? ' layui-hide' : ''; ?>" title="通知消息">
						<i class="layui-icon layui-icon-notice"></i>
						<span class="layui-badge message-dot layui-hide">0</span>
					</a>
				</li>
				<li class="layui-nav-item layui-hide-xs" lay-unselect="">
					<a href="javascript:;" class="fullscreen" title="全屏显示">
						<i class="layui-icon layui-icon-screen-full"></i>
					</a>
				</li>
				<li class="layui-nav-item layui-hide layui-show-md-inline-block">
					<a href="javascript:;" class="info">
						<img class="layui-nav-img">
						<span class="username"></span>
					</a>
					<dl class="layui-nav-child">
						<dd><a href="page/user_info.php" class="set_info"><span>个人资料</span></a></dd>
						<dd><a href="javascript:;" class="set_password"><span>修改密码</span></a></dd>
						<hr />
						<dd><a href="javascript:;" class="quit"><span>退出</span></a></dd>
					</dl>
				</li>
				<li class="layui-nav-item" lay-header-event="menuRight" lay-unselect title="界面设置">
					<a href="javascript:;">
						<i class="layui-icon layui-icon-more-vertical"></i>
					</a>
				</li>
			</ul>
		</div>
		<!-- 左侧 -->
		<div class="layui-side">
			<div class="layui-side-scroll layui-scrollbar">
				<div class="layui-logo">
					<img src="images/logo.png?v=<?php echo $web->v; ?>" />
					<h1><?php echo $web->sys['title']; ?></h1>
				</div>
				<ul class="layui-nav layui-nav-tree" lay-filter="nav">
					<?php echo $web->_menu(); ?>
				</ul>
			</div>
		</div>
		<!-- 手机遮挡层 -->
		<div class="shade"></div>
		<!-- 主体 -->
		<div class="layui-body">
			<div class="layui-tab layui-tab-brief" lay-allowClose="true" lay-filter="tab">
				<ul class="layui-tab-title"></ul>
				<div class="layui-tab-content"></div>
			</div>
			<div class="layui-icon layui-icon-left page-left"></div>
			<div class="layui-icon layui-icon-right page-right"></div>
			<div class="layui-icon layui-icon-down page-batch"></div>
		</div>
		<!-- 底部 -->
		<div class="layui-footer">
			<div>Copyright © <?php echo date("Y"); ?> <?php echo $web->sys['title']; ?> All rights reserved</div>
			<div>System v<?php echo $web->v; ?></div>
		</div>
	</div>
	<div class="load">
		<div class="load-icon">
			<span class="load-1"></span>
			<span class="load-2"></span>
			<span class="load-3"></span>
			<span class="load-4"></span>
		</div>
	</div>
	<div class="temp" style="opacity: 0;position: absolute;left: 0;top: 0;"></div>
	<script>
		window.admin = '<?php echo $web->admin ? "true" : "false"; ?>';
		window.anim = '<?php echo $web->user['anim_state']; ?>';
		window.time_state = '<?php echo $web->user['time_state']; ?>';
		window.chat = '<?php echo $web->user['show_chat']; ?>';
		window.msg_time = '<?php echo $web->sys['msg_time']; ?>';
		window.server = {
			UserIp: '<?php echo $web->ip; ?>',
			PlugUrl: '<?php echo $web->server; ?>',
			version: '<?php echo $web->sys['version']; ?>'
		};
	</script>
	<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
	<script src="js/api.js?v=<?php echo $web->v; ?>"></script>
	<script src="js/Sortable.min.js?v=<?php echo $web->v; ?>"></script>
	<script src="js/admin.js?v=<?php echo $web->v; ?>"></script>
</body>

</html>