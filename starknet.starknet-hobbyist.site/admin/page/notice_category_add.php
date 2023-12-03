<?php
include '../php/api.php';
class web extends _api
{
	public function _set()
	{
		$sql = $this->getSql('notice_category', ['name', 'show'], false, ['name']);
		$id = $this->is('id');
		$sql = $id != '' ? $sql['upd'] : $sql['add'];
		$this->run($sql, false);
	}
};
$web = new web(2, "*");
$web->method('notice_category');
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>新增分类分类</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span class="layui-must" title="必填项">*</span>
			<span>分类名称</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="name" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['name']) ? $web->info['name'] : ''; ?>" placeholder="请输入分类名称" />
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span>是否显示</span>
		</label>
		<div class="layui-input-block">
			<input type="radio" name="show" value="1" title="显示" lay-filter="show" <?php echo isset($web->info['show']) ? ($web->info['show'] == '1' ? 'checked' : '') : 'checked'; ?> />
			<input type="radio" name="show" value="0" title="隐藏" lay-filter="show" <?php echo isset($web->info['show']) ? ($web->info['show'] == '0' ? 'checked' : '') : ''; ?> />
		</div>
	</div>
	<div class="layui-footer layui-nobox">
		<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">保存</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
	</div>
</body>
<script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
	form.on('submit(submit)', function(data) {
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