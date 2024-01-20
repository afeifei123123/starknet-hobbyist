<?php
include '../php/api.php';
class _web extends _api
{

	//保存或者新增面板
	public function _set()
	{
		$sql = $this->getSql('home_card', ['id', 'name', 'icon', 'sql', 'color', 'contrast', 'sql1', 'url']);
		$id = $this->is('id');
		$sql = $id != "" ? $sql['upd'] : $sql['add'];
		$this->run($sql, false);
	}
}
$web = new _web(2, "id", false, true);
$web->method('home_card');
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>设置卡片面板</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
	<style>
		.upload-item>img {
			left: -40px;
			top: -40px;
			filter: drop-shadow(40px 40px <?php echo isset($web->info['color']) ? $web->info['color'] : ''; ?>);
			background-image: none;
		}

		.upload-item::after,
		.upload-item::before {
			display: none;
		}

		.layui-card-body {
			padding: 10px 0px;
		}

		.php {
			color: #ff007f;
		}

		.php.this {
			background-color: #ffffff;
		}

		.sql-item {
			height: 35px;
			line-height: 35px;
			border-bottom: 1px solid #F0F0F0;
			display: flex;
			cursor: pointer;
		}

		.sql-item:hover {
			background-color: rgba(0, 0, 0, 0.03);
		}

		.sql-name {
			width: 100px;
			text-indent: 10px;
			color: #ff007f;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			word-break: break-all;
		}

		.sql-value {
			width: 100px;
			text-indent: 10px;
			color: #0db3b3;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			word-break: break-all;
		}
	</style>
</head>

<body class="layui-form">
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span class="layui-must">*</span>
			<span>卡片名称</span>
		</label>
		<div class="layui-input-inline">
			<input type="text" name="name" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['name']) ? $web->info['name'] : ''; ?>" placeholder="请输入卡片名称"/>
		</div>
		<div class="layui-form-mid layui-word-aux">
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span class="layui-must">*</span>
			<span>卡片图标</span>
		</label>
		<div class="layui-input-inline">
			<input type="text" name="icon" class="layui-input layui-hide" value="<?php echo isset($web->info['icon']) ? $web->info['icon'] : ''; ?>" />
			<div class="upload-item" style="width: 40px;height: 40px;" path="upload/home/" del="true">
				<?php echo isset($web->info['icon']) ? "<img src='{$web->info['icon']}' />" : ''; ?>
			</div>
		</div>
		<div class="layui-form-mid layui-word-aux">
			<span>建议尺寸 40x40，并且图片为png透明类型</span>
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span>跳转地址</span>
		</label>
		<div class="layui-input-inline">
			<input type="text" name="url" class="layui-input" value="<?php echo isset($web->info['url']) ? $web->info['url'] : ''; ?>" placeholder="请输入跳转地址"/>
		</div>
		<div class="layui-form-mid layui-word-aux">
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span>图标颜色</span>
		</label>
		<div class="layui-input-inline">
			<input type="text" name="color" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['color']) ? $web->info['color'] : ''; ?>" placeholder="请选择图标颜色"/>
		</div>
		<div class="layui-form-mid layui-word-aux">
			<div id="color"></div>
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span class="layui-must">*</span>
			<span>查询SQL</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="sql" class="layui-input" lay-verify="required" value="<?php echo isset($web->info['sql']) ? $web->info['sql'] : ''; ?>" phpm="phpm" placeholder="请生成SQL"/>
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			<span>数据对比</span>
		</label>
		<div class="layui-input-inline">
			<input type="radio" name="contrast" value="1" title="开启" lay-filter="contrast" <?php echo isset($web->info['contrast']) ? ($web->info['contrast'] == '1' ? 'checked' : '') : ''; ?> />
			<input type="radio" name="contrast" value="0" title="关闭" lay-filter="contrast" <?php echo isset($web->info['contrast']) ? ($web->info['contrast'] == '0' ? 'checked' : '') : 'checked'; ?> />
		</div>
		<div class="layui-form-mid layui-word-aux">
			<span>开启后将当前查询数据与上面的查询数据进行对比</span>
		</div>
	</div>
	<div class="layui-form-item contrast-box <?php echo isset($web->info['contrast']) ? ($web->info['contrast'] == '1' ? '' : 'layui-hide') : 'layui-hide'; ?>">
		<label class="layui-form-label">
			<span class="layui-must">*</span>
			<span>查询SQL</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="sql1" class="layui-input" <?php echo isset($web->info['contrast']) ? ($web->info['contrast'] == '1' ? 'lay-verify="required"' : '') : 'layui-hide'; ?> value="<?php echo isset($web->info['sql1']) ? $web->info['sql1'] : ''; ?>" phpm="phpm" placeholder="请生成SQL"/>
		</div>
	</div>
	<div class="layui-footer layui-nobox">
		<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">保存</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
	</div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
	layui.colorpicker.render({
		elem: '#color',
		size: 'xs',
		color: '<?php echo isset($web->info['color']) ? $web->info['color'] : ''; ?>',
		change: function(color) {
			$('[name=color]').val(color);
			$('.upload-item>img').css('filter', 'drop-shadow(40px 40px ' + color + ')');
		},
		done: function(color) {
			$('[name=color]').val(color);
			$('.upload-item>img').css('filter', 'drop-shadow(40px 40px ' + color + ')');
		}
	});
	$(document).on('mouseover', '.sql-item', function() {
		var index = $('.sql-item').index(this);
		$('.php.this').removeClass('this');
		$(".php").eq(index).addClass("this");
	});
	$(document).on('mouseout', '.sql-item', function() {
		$('.php.this').removeClass('this');
	});
	form.on('radio(contrast)', function(data) {
		if (data.value == '1') {
			$(".contrast-box").removeClass('layui-hide');
			$(".contrast-box").find('[name=sql1]').attr('lay-verify', 'required');
			return true;
		}
		$(".contrast-box").addClass('layui-hide');
		$(".contrast-box").find('[name=sql1]').removeAttr('lay-verify');
	});
	form.on("submit(submit)", function(data) {
		$.ajax({
			url: api.url('set') + "&id=<?php echo $web->is('id'); ?>",
			type: 'POST',
			dataType: 'json',
			data: data.field,
			beforeSend: function() {
				parent.layer.msg("正在提交", {
					icon: 16,
					shade: 0.05,
					time: false
				});
			},
			success: function(data) {
				parent.layer.msg(data.msg, {
					icon: data.code
				}, function() {
					if (data.code == 1) {
						parent.card.get();
						var index = parent.layer.getFrameIndex(window.name);
						parent.layer.close(index);
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