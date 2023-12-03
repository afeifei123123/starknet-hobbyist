<?php
include '../php/api.php';
class web extends _api
{
	/**
	 * 获取分类公告
	 * @return void
	 */
	public function _data()
	{

		$this->table('notice_category', ['name'], '`id`,`name`,`show`,`found_date`,
		(SELECT count(`id`) FROM `notice_list` WHERE notice_list.cid = notice_category.id limit 1) AS `count`', ' ORDER BY `indexs`,`id` ASC');
	}

	/**
	 * 删除分类
	 * @return void
	 */
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
			// 查询分类下是否有公告
			$sql = "SELECT `id` FROM `notice_list` WHERE `cid` = '{$value['id']}'";
			$res = $this->conn->query($sql);
			if ($res->num_rows > 0) {
				// 遍历删除公告寄内容
				while ($row = $res->fetch_assoc()) {
					$fileName = '../upload/notice/' . $row['id'] . '.html';
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
					$sql = "DELETE FROM `notice_list` WHERE `id` = '{$row['id']}'";
					$this->run($sql);
				}
			}

			// 删除分类
			$sql = "DELETE FROM `notice_category` WHERE `id` = '{$value['id']}'";
			$this->run($sql);
		}

		$this->res('删除成功');
	}

	/**
	 * 更新状态
	 * @return void
	 */
	public function _set()
	{
		$this->ajax(['field', 'id', 'value']);
		$field = $_REQUEST['field'];
		$id = intval($_REQUEST['id']);
		$value = intval($_REQUEST['value']);

		if ($field == 'show') {
			$sql = "UPDATE `notice_category` SET `show` = '{$value}' WHERE `id` = '{$id}'";
			$this->run($sql, false);
		}

		$this->res('暂时不支持修改该字段', 5);
	}
};
$web = new web(2);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>公告分类</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
	<style>
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
						<p>温馨提示：分类排序可拖动排序，排序越小越靠前</p>
					</div>
				</div>
			</div>
		</div>
		<div class="layui-col-md12">
			<div class="layui-card">
				<div class="layui-card-body">
					<div class="layui-form-item">
						<label class="layui-form-label">分类名称</label>
						<div class="layui-input-inline">
							<input type="text" name="name" lay-verify="required" class="layui-input" placeholder="请输入分类名称" />
						</div>
						<div class="layui-input-inline">
							<button class="layui-btn layui-btn-sm layui-btn-normal search">
								<i class="layui-icon layui-icon-search"></i>
								<span>查询</span>
							</button>
						</div>
					</div>
					<table id="notice_category" lay-filter="notice_category"></table>
				</div>
			</div>
		</div>
	</div>
</body>
<script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/Sortable.min.js"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script type="text/html" id="notice_categoryTool">
	<div class="layui-btn-container">
		<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">
			<i class="layui-icon layui-icon-add-1"></i>
			<span>新增分类</span>
		</button>
	</div>
</script>

<!-- [是否显示]开关 -->
<script type='text/html' id='showSwitchTool'>
	<input type='checkbox' value='{{d.id}}' lay-skin='switch' lay-filter='show' title="显示|隐藏" {{ d.show == '1' ? 'checked' : '' }} />
</script>

<!-- [操作]删除按钮 -->
<script type='text/html' id='caozuoDelTool'>
	<a class='layui-table-link' lay-event='details' title='查看'>查看</a>
	<span class='layui-table-divide'></span>
	<a class='layui-table-link' lay-event='edit' title='编辑'>编辑</a>
	<span class='layui-table-divide'></span>
	<a class='layui-table-del' lay-event='del'>删除</a>
</script>

<script>
	table.render({
		elem: "#notice_category",
		url: api.url('data'),
		page: true,
		title: "公告分类",
		toolbar: "#notice_categoryTool",
		skin: "line",
		where: where(),
		cols: [
			[{
				field: 'sort',
				title: '排序',
				width: 100,
				align: 'center',
				templet: d => {
					return '<div class="lay-sort" data-id="' + d.id + '"></div>';
				}
			}, {
				field: 'name',
				title: '分类名称',
				width: 250,
				align: 'center',
				templet: d => {
					return `<a class="layui-table-link link" lay-event="details" title="查看">${d.name}</a>`;
				}
			}, {
				field: 'count',
				title: '公告数量',
				width: 250,
				align: 'center',
				templet: d => {
					return d.count > 0 ? d.count + '条' : '-';
				}
			}, {
				field: 'found_date',
				title: '创建时间',
				minWidth: 200
			}, {
				field: 'show',
				title: '是否显示',
				templet: '#showSwitchTool',
				width: 90,
				align: 'center',
				fixed: 'right'
			}, {
				field: 'caozuo',
				title: '操作',
				templet: '#caozuoDelTool',
				width: 200,
				fixed: 'right',
				align: 'center'
			}]
		],
		done: function(res) {
			api.sort(res);
		}
	});
	table.on("toolbar(notice_category)", function(obj) {
		var checkStatus = table.checkStatus(obj.config.id);
		switch (obj.event) {
			case 'add':
				add();
				break;
			case 'Del':
				Del(checkStatus);
				break;
				defalut:
					break;
		};
	});

	form.on('switch(show)', function(obj) {
		var json = {
			field: 'show',
			value: obj.elem.checked == true ? 1 : 0,
			data: {
				id: this.value
			}
		};
		update(json, function(data) {
			layer.msg(data.msg, {
				icon: data.code
			});
			if (data.code != '1') {
				$(obj.elem).prop('checked', !obj.elem.checked);
				form.render('checkbox');
			}
		});
	});

	function add() {
		layer.open({
			type: 2,
			title: '新增分类',
			area: ['450px', '250px'],
			maxmin: false,
			content: 'notice_category_add.php',
			shade: 0.3
		});
	}

	function edit(obj) {
		var id = obj.data.id;
		layer.open({
			type: 2,
			title: '修改分类',
			area: ['437px', '250px'],
			maxmin: false,
			content: 'notice_category_add.php?id=' + id,
			shade: 0.3
		});
	}

	function del(obj) {
		layer.confirm('确定删除此分类吗？', function() {
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

	function update(obj, success) {
		$.ajax({
			url: api.url('set'),
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				layer.msg('正在更新', {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			data: {
				field: obj.field,
				id: obj.data.id,
				value: obj.value,
			},
			success: function(data) {
				if (success != undefined) {
					success(data);
				} else {
					layer.msg(data.msg, {
						icon: data.code
					});
					if (data.code != '1') {
						reload('notice_category');
					}
				}
			},
			error: function(data) {
				console.log(data);
				layer.msg(data.responseText, {
					icon: 5
				});
			}
		});
	}

	function details(obj) {
		window.parent.App.add('page/notice_list_data.php?cid=' + obj.data.id, obj.data.name + '[公告列表]', '公告管理');
	}

	table.on('tool(notice_category)', function(obj) {
		var data = obj.data;
		switch (obj.event) {
			case 'edit':
				edit(obj);
				break;
			case 'del':
				del(obj);
				break;
			case 'details':
				details(obj);
				break;
				defalut:
					break;
		};
	});
</script>

</html>