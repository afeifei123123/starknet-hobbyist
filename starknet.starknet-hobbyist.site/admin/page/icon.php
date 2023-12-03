<?php
include '../php/api.php';
class _web extends _api
{
	//获取数据
	public function _init()
	{
		$url = "http://www.layuicdn.com/layui/css/layui.css?v=20201111001";
		$str = $this->curl($url, [], '', false);
		$is = preg_match_all('/layui-icon-\w*-?\w*/', $str, $arr);
		if (!$is) {
			$this->res('无法获取图标文件', 3);
		}
		$data = [];
		$str = "";
		$search = $this->is('search');
		foreach ($arr[0] as $icon) {
			if ($search == '') {
				$str .= $icon . ",";
				$data[] = $icon;
			} else {
				if (strstr($icon, $search)) {
					$data[] = $icon;
				}
			}
		}
		//exit($str);
		$count = count($data);
		$page = intval($this->is('page', 1));
		$limit = intval($this->is('limit', 16));
		$start = ($page - 1) * $limit;
		$data = array_slice($data, $start, $limit);
		$content = "";
		foreach ($data as $value) {
			$r = rand(0, 200);
			$g = rand(0, 255);
			$b = rand(0, 200);
			$content .= "<li><i class='layui-icon {$value}'></i></li>";
		}
		$this->search = $search;
		$this->content = $content;
		$this->count = $count;
		$this->limit = $limit;
		$this->page = $page;
	}
}
$web = new _web(2, "id", false, true);
$web->_init();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>分页案例</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
	<style>
		body {
			overflow: hidden;
		}

		.main {
			width: min-content;
			margin: auto;
		}

		.content {
			display: grid;
			grid-gap: 10px;
			grid-template-columns: 40px 40px 40px 40px;
			padding: 10px;
			border-radius: 4px;
			width: min-content;
		}

		.content>li {
			line-height: 40px;
			background-color: #FFFFFF;
			border: 1px solid #F0F0F0;
			text-align: center;
			border-radius: 4px;
			cursor: pointer;
		}

		.content>li:hover {
			background-color: rgba(0, 0, 0, 0.05);
		}

		#page {
			text-align: center;
		}

		.layui-input,
		.layui-textarea {
			width: 90%;
			margin: 10px auto;
		}
	</style>
</head>

<body>
	<div class="main">
		<div class="layui-form-item">
			<input type="text" name="search" autocomplete="off" class="layui-input" placeholder="搜索图标名称" value="<?php echo $web->search; ?>" />
		</div>
		<div class="content">
			<?php echo $web->content; ?>
		</div>
		<div class="footer">
			<div id="page"></div>
		</div>
	</div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
	laypage.render({
		elem: 'page',
		count: <?php echo $web->count; ?>,
		limit: <?php echo $web->limit; ?>,
		curr: <?php echo $web->page; ?>,
		layout: ['prev', 'next'],
		jump: function(page, first) {
			if (page.curr != <?php echo $web->page; ?>) {
				var search = $("[name=search]").val();
				location.href = "icon.php?page=" + page.curr + "&search=" + search;
			}
		}
	});
	$("[name=search]").keydown(function(e) {
		if (e.keyCode == 13) {
			var search = $("[name=search]").val();
			location.href = "icon.php?search=" + search;
		}
	});
	$(document).on('click', '.content>li', function() {
		var c = $(this).find('i').attr('class');
		var index = parent.layer.getFrameIndex(window.name);
		parent.icon.attr('class', c);
		parent.icon.prev().val(c);
		parent.layer.close(index);
	});
</script>

</html>