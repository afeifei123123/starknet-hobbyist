<?php
include '../php/api.php';
class web extends _api
{
	public function _data()
	{
		$this->table('notice_list', ['cid:=', 'name'], '`id`,`title`,`date`,`is_new`,`found_date`', ' ORDER BY `date` DESC');
	}

	public function _del()
	{
		$this->ajax(['item']);
		$item = $_REQUEST['item'];

		//判断必须为数组
		if (!is_array($item)) {
			$this->res('item必须为数组', 5);
		}

		//判断数组是否为空
		if (empty($item)) {
			$this->res('item不能为空', 5);
		}

		//循环删除
		foreach ($item as $key => $value) {
			$fileName = '../upload/notice/' . $value['id'] . '.html';
			// 读取文件 删除里面图片文件
			$content = file_get_contents($fileName);
			// 正则匹配图片
			preg_match_all('/<img.*?src="(.*?)".*?>/is', $content, $img);
			// 删除图片
			foreach ($img[1] as $k => $v) {
				// 提取出文件名称
				$file = substr($v, strrpos($v, '/') + 1);
				$url = '../upload/notice/' . $file;
				// 判断文件是否存在
				if (file_exists($url)) {
					// 删除文件
					unlink($url);
				}
			}
			// 删除文件
			unlink($fileName);

			// 删除数据库
			$sql = "DELETE FROM `notice_list` WHERE `id` = '{$value['id']}'";
			$this->run($sql);
		}

		$this->res('删除成功');
	}

	public function _remote()
	{
		$this->ajax(['cid', 'url']);
		$cid = intval($_REQUEST['cid']);
		$url = $_REQUEST['url'];

		// 删除空格和换行
		$url = trim($url);

		$html = file_get_contents($url);

		if (empty($html)) {
			$this->res('获取失败', 5);
		}

		// 获取文件名之前的url
		$host = substr($url, 0, strrpos($url, '/') + 1);

		// 正则匹配图片 把图片下载到本地../upload/notice/ 替换图片路径
		preg_match_all('/<img.*?src="(.*?)".*?>/is', $html, $img);

		// 判断是否有图片
		if (isset($img[1])) {
			$count = count($img[1]);
			// 下载图片
			foreach ($img[1] as $k => $v) {
				// 提取出文件名称
				$file = substr($v, strrpos($v, '/') + 1);
				$url = $v;
				// 判断如果没有http开头则为相对路径
				if (substr($v, 0, 4) != 'http') {
					$url = $host . $v;
				}

				$progress = '开始下载图片' . ($k + 1) . '/' . $count;
				$this->sendMessage($this->id, 0, $this->user['picture'], $progress, '', 7);

				// 下载图片
				$img = file_get_contents($url);
				// 保存图片
				file_put_contents('../upload/notice/' . $file, $img);

				// 替换图片路径
				$html = str_replace($v, '../upload/notice/' . $file, $html);
			}
		}

		// 创建一个新的sql
		$name = substr($url, strrpos($url, '/') + 1, strrpos($url, '.') - strrpos($url, '/') - 1);
		$sql = "INSERT INTO `notice_list` (`cid`,`title`,`date`) VALUES ('{$cid}','{$name}',NOW())";
		$res = $this->run($sql);
		// 获取插入的id
		$id = $this->conn->insert_id;

		// 删除白色背景background-color:#ffffff
		$html = str_replace('background-color:#ffffff', '', $html);

		// 删除空格
		$html = str_replace('&#xa0;', '', $html);

		// 正则删text-indent
		$html = preg_replace('/text-indent:.*?;/is', '', $html);

		// 正则删除margin-top
		$html = preg_replace('/margin-top:.*?;/is', '', $html);

		// 正则删除margin-bottom
		$html = preg_replace('/margin-bottom:.*?;/is', '', $html);

		// 替换#0f1419 为 #ffffff
		$html = str_replace('#0f1419', '#ffffff', $html);

		// 替换#000000 为 #ffffff
		$html = str_replace('#000000', '#ffffff', $html);

		// 写入文件
		file_put_contents('../upload/notice/' . $id . '.html', $html);

		$this->res('获取成功');
	}
};
$web = new web(2);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>公告</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
	<style>
		.layui-badge {
			margin-right: 10px;
		}

		.link.layui-table-link[lay-event] {
			color: var(--link) !important;
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
						<p>温馨提示：公告</p>
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
							<input type="text" name="cid" lay-verify="required" class="layui-input layui-hide" value="<?php echo $web->is('cid'); ?>" placeholder="请输入分类编号" />
							<input type="text" name="name" lay-verify="required" class="layui-input" placeholder="请输入分类名称" />
						</div>
						<div class="layui-input-inline">
							<button class="layui-btn layui-btn-sm layui-btn-normal search">
								<i class="layui-icon layui-icon-search"></i>
								<span>查询</span>
							</button>
						</div>
					</div>
					<table id="notice_list" lay-filter="notice_list"></table>
				</div>
			</div>
		</div>
	</div>
</body>
<script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script type="text/html" id="notice_listTool">
	<div class="layui-btn-container">
		<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">
			<i class="layui-icon layui-icon-add-1"></i>
			<span>新增公告</span>
		</button>
		<button class="layui-btn layui-btn-sm layui-btn-plug-danger" lay-event="Del">
			<i class="layui-icon layui-icon-delete"></i>
			<span>删除公告</span>
		</button>
		<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="remote">
			<i class="layui-icon layui-icon-link"></i>
			<span>远程获取</span>
		</button>
		<span class='layui-table-divide'></span>
		<a href="https://www.aconvert.com/cn/document/doc-to-html/" target="_blank" class="layui-table-link link">
			<i class="layui-icon layui-icon-link"></i>
			<span>点击跳转到转换网站进行格式转换</span>
		</a>
	</div>
</script>

<!-- [操作]删除按钮 -->
<script type='text/html' id='caozuoDelTool'>
	<a class='layui-table-link' lay-event='edit' title='修改'>修改</a>
	<span class='layui-table-divide'></span>
	<a class='layui-table-link' lay-event='url' title='链接'>链接</a>
	<span class='layui-table-divide'></span>
	<a class='layui-table-del' lay-event='del'>删除</a>
</script>
<script>
	table.render({
		elem: "#notice_list",
		url: api.url('data'),
		page: true,
		title: "公告",
		toolbar: "#notice_listTool",
		skin: "line",
		where: where(),
		cols: [
			[{
				type: 'checkbox'
			}, {
				field: 'date',
				title: '发布时间',
				width: 200
			}, {
				field: 'title',
				title: '标题',
				minWidth: 300,
				templet: function(d) {
					const is_new = d.is_new == '1' ? '<span class="layui-badge">new</span>' : '';
					return `<a class="layui-table-link link" lay-event="edit" title="修改">${is_new}${d.title}</a>`;
				}
			}, {
				field: 'found_date',
				title: '创建时间',
				width: 200
			}, {
				field: 'caozuo',
				title: '操作',
				templet: '#caozuoDelTool',
				width: 150,
				fixed: 'right',
				align: 'center'
			}]
		]
	});
	table.on("toolbar(notice_list)", function(obj) {
		var checkStatus = table.checkStatus(obj.config.id);
		switch (obj.event) {
			case 'add':
				add();
				break;
			case 'Del':
				Del(checkStatus);
				break;
			case 'remote':
				remote();
				break;
		};
	});


	function edit(obj) {
		layer.open({
			type: 2,
			title: '修改公告',
			area: ['1200px', '700px'],
			maxmin: true,
			content: 'notice_list_add.php?id=' + obj.data.id,
			shade: 0.3
		});
	}

	function add() {
		layer.open({
			type: 2,
			title: '新增公告',
			area: ['1200px', '700px'],
			maxmin: true,
			content: 'notice_list_add.php?cid=<?php echo $web->is("cid"); ?>',
			shade: 0.3
		});
	}

	function del(obj) {
		layer.confirm('确定删除此公告吗？', function() {
			var arr = [];
			arr[0] = obj.data;
			$.ajax({
				url: api.url('del'),
				type: 'POST',
				dataType: 'json',
				beforeSend: function() {
					layer.msg('删除中', {
						icon: 16,
						shade: 0.05,
						time: false
					});
				},
				data: {
					item: arr
				},
				success: function(data) {
					layer.msg(data.msg, {
						icon: data.code
					});
					if (data.code == '1') {
						obj.del();
					}
				},
				error: function(data) {
					console.log(data);
					layer.msg(data.responseText, {
						icon: 5
					});
				}
			});
		});
	}

	table.on('tool(notice_list)', function(obj) {
		var data = obj.data;
		switch (obj.event) {
			case 'edit':
				edit(obj);
				break;
			case 'del':
				del(obj);
				break;
			case 'url':
				window.open('../../news_detail.php?id=' + data.id);
				break;
		};
	});


	function Del(checkStatus) {
		layer.confirm('确定删除选中的公告吗？', function() {
			$.ajax({
				url: api.url('del'),
				type: 'POST',
				dataType: 'json',
				beforeSend: function() {
					layer.msg('删除中', {
						icon: 16,
						shade: 0.05,
						time: false
					});
				},
				data: {
					item: checkStatus.data
				},
				success: function(data) {
					layer.msg(data.msg, {
						icon: data.code
					});
					if (data.code == '1') {
						reload('notice_list');
					}
				},
				error: function(data) {
					console.log(data);
					layer.msg(data.responseText, {
						icon: 5
					});
				}
			});
		});
	}

	function remote() {
		layer.prompt({
			title: '远程获取',
			formType: 0,
			area: ['800px', '350px'],
			success: function(layero, index) {
				const input = layero.find('.layui-layer-input');
				input.attr('placeholder', '请输入文件链接');
			}
		}, (value, index) => {
			$.ajax({
				url: api.url('remote'),
				type: 'POST',
				dataType: 'json',
				beforeSend: function() {
					layer.msg('获取中', {
						icon: 16,
						shade: 0.05,
						time: false
					});
				},
				data: {
					url: value,
					cid: '<?php echo $web->is("cid"); ?>'
				},
				success: function(data) {
					layer.msg(data.msg, {
						icon: data.code
					});
					if (data.code == '1') {
						layer.close(index);
						reload('notice_list');
					}
				},
				error: function(data) {
					console.log(data);
					layer.msg(data.responseText, {
						icon: 5
					});
				}
			});
		});
	}
</script>

</html>