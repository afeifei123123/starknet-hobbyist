<?php
include '../php/api.php';
class _web extends _api
{
    public function _init()
    {
        $sql = "SELECT `id`,`name` FROM  `juris_data`;";
        $res = $this->conn->query($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $data[] = [
                    'title' => $row['name'],
                    'value' => $row['id']
                ];
            }
        }
        $this->list = json_encode($data);
        $id = $this->is('id');
        $juris = [];
        $sql = "SELECT `juris_id` FROM  `juris_list` WHERE `roles_id` = {$id};";
        $res = $this->conn->query($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $juris[] = $row['juris_id'];
            }
        }
        $this->value = json_encode($juris);
        $this->id = $id;
    }

    public function _set()
    {
        $d = $this->is('data', []);
        $r = $this->is('id');
        $q = "DELETE FROM `juris_list` WHERE `roles_id` = {$r};";
        $this->run($q);
        foreach ($d as $k) {
            $j = $k['value'];
            $this->db->add('juris_list', [
                'roles_id' => $r,
                'juris_id' => $j
            ]);
        }
        $this->res();
    }
};
if (isset($_REQUEST['method'])) {
    $web = new _web(2, "*", false, true);
    $method = $_REQUEST['method'];
    $list = ['set'];
    if (!in_array($method, $list)) {
        $web->res("请求“{$method}”方法不存在", 3);
    }
    $f = "\$web->_{$method}();";
    eval($f);
    exit();
} else {
    $web = new _web(2, 'id', false, true);
    $web->_init();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>角色权限列表</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
</head>

<body class="layui-form">
    <div>
        <div id="list" class="demo-transfer"></div>
    </div>
    <div class="layui-footer layui-nobox">
        <button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">保存</button>
        <button class="layui-btn layui-btn-primary layui-btn-sm" lay-close="true">取消</button>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    var transfer = layui.transfer;
    var list = <?php echo $web->list; ?>;
    transfer.render({
        elem: '#list',
        data: list,
        title: ['全部权限', '角色权限'],
        value: <?php echo $web->value; ?>,
        showSearch: true,
        id: 'list'
    })
    form.on("submit(submit)", function(data) {
        var data = transfer.getData('list');
        $.ajax({
            url: api.url('set') + "&id=<?php echo $web->id; ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                data: data
            },
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