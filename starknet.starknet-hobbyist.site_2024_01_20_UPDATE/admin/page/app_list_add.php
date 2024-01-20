<?php
include '../php/api.php';
class web extends _api
{
	public function _set()
	{
		$id = $this->is('id');
		if ($id) {
			// 修改
			$this->ajax(['name', 'img'], false, false);
			$name = $_REQUEST['name'];
			$img = $_REQUEST['img'];
			$content = $_REQUEST['content'];

			$sql = "UPDATE `app_list` SET `name` = '{$name}', `img` = '{$img}' WHERE `id` = '{$id}'";
			$res = $this->run($sql);

			$fileName = '../upload/app/' . $id . '.html';
			// 写入文件
			file_put_contents($fileName, $content);

			$this->res('修改APP成功');
		} else {
			// 新增
			$this->ajax(['name', 'img'], false, false);
			$name = $_REQUEST['name'];
			$img = $_REQUEST['img'];
			$content = $_REQUEST['content'];

			$sql = "INSERT INTO `app_list` (`name`, `img`) VALUES ('{$name}', '{$img}')";
			$res = $this->run($sql);
			// 获取插入的id
			$id = $this->conn->insert_id;
			$fileName = '../upload/app/' . $id . '.html';
			// 写入文件
			file_put_contents($fileName, $content);

			$this->res('新增APP成功');
		}
	}
};
$web = new web(2, "*");
$web->method('app_list');
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>新增APP</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span class="layui-must" title="必填项">*</span>
			<span>APP名称</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="name" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['name']) ? $web->info['name'] : ''; ?>" />
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span class="layui-must">*</span>
			<span>APP图标</span>
		</label>
		<div class="layui-input-block">
			<div class="upload-item" path="upload/app/" style="width:40px;height:40px;">
				<?php
				$img = $web->info['img'] ?? '';
				if ($img == '') {
					echo '<span>上传图片</span>';
				} else {
					echo '<img src="' . $img . '" />';
				}
				?>
			</div>
			<label>
				<input type="text" name="img" class="layui-input layui-hide" lay-verify="required" value="<?php echo $web->info['img'] ?? ''; ?>">
			</label>
			<div class="layui-form-mid layui-word-aux" style="float: none;"><span>* 建议图片尺寸40*40</span></div>
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span class="layui-must" title="必填项">*</span>
			<span>APP介绍</span>
		</label>
		<div class="layui-input-block">
			<div id="content"></div>
		</div>
	</div>
	<div class="layui-footer layui-nobox">
		<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">保存</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
	</div>
</body>
<script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script type="text/javascript" src="https://cdn.20ps.cn/dist/js/wangEditor.min.js"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
	// 编辑器
	const E = window.wangEditor;
	window.edit = new E('#content');
	window.edit.config.uploadImgServer = window.api.url('upload', '../?method=') + '&path=upload/app/&wangEditor=true';
	window.edit.config.uploadImgAccept = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
	window.edit.config.uploadFileName = 'file';
	window.edit.config.height = 350;
	window.edit.config.focus = false;
	window.edit.config.menus = [
		'head',
		'bold',
		'fontSize',
		'fontName',
		'italic',
		'underline',
		'strikeThrough',
		'indent',
		'lineHeight',
		'foreColor',
		'backColor',
		'link',
		'list',
		'todo',
		'justify',
		'quote',
		'emoticon',
		'image',
		'video',
		'table',
		'code',
		'splitLine',
		'undo',
		'redo'
	];
	window.edit.config.colors = [
		'#16BAAA',
		'#16B777',
		'#1E9FFF',
		'#FF5722',
		'#FFB800',
		'#01AAED',
		'#999999',
		'#CC0000',
		'#FF8C00',
		'#FFD700',
		'#90EE90',
		'#00CED1',
		'#1E90FF',
		'#C71585',
		'#00BABD',
		'#FF7800',
		'#FAD400',
		'#393D49',
		'#000000',
		'#ff0000'
	];
	window.edit.config.languageType = [
		'Bash',
		'C',
		'C#',
		'C++',
		'CSS',
		'Java',
		'JavaScript',
		'JSON',
		'TypeScript',
		'Plain text',
		'Html',
		'XML',
		'SQL',
		'Go',
		'Kotlin',
		'Lua',
		'Markdown',
		'PHP',
		'Python',
		'Shell Session',
		'Ruby',
	]
	window.edit.create();
	const content = `<?php
						if (isset($web->info['id'])) {
							$fileName = '../upload/app/' . $web->info['id'] . '.html';
							if (file_exists($fileName)) {
								echo str_replace("`",'',file_get_contents($fileName));
							}
						}
						?>`;
	window.edit.txt.html(content);
	form.on('submit(submit)', function(data) {
		data.field.content = window.edit.txt.html();
		$.ajax({
			url: api.url('set') + "&id=<?php echo $web->is('id'); ?>",
			type: 'POST',
			dataType: 'json',
			data: data.field,
			beforeSend: () => {
				$("[lay-filter='submit']").addClass('layui-disabled');
				parent.layer.msg("正在提交", {
					icon: 16,
					shade: 0.05,
					time: false
				})
			},
			success: function(r) {
				parent.layer.msg(r.msg, {
					icon: r.code
				}, () => {
					$("[lay-filter='submit']").removeClass('layui-disabled');
					if (r.code == 1) {
						var index = parent.layer.getFrameIndex(window.name);
						parent.layer.close(index);
						parent.reload();
					}
				});
			},
			error: r => layer.alert(r.responseText, {
				icon: 2
			})
		});
		return false;
	});
</script>

</html>