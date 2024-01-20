<?php
include './admin/php/api.php';
class news extends _api
{
    var $info = [
        'title' => '',
        'content' => '',
        'date' => '',
        'name' => '公告详情'
    ];
    /**
     * 初始化
     * @return void
     */
    public function init()
    {
        $id = intval($this->is('id'));
        if (!$id) {
            $this->res('访问页面不存在', 5);
        }

        $fileName = './admin/upload/notice/' . $id . '.html';
        if (!file_exists($fileName)) {
            $this->res('访问页面不存在', 5);
        }

        $sql = "SELECT `title`,`date`,(SELECT `name` FROM `notice_category` WHERE notice_category.id = notice_list.cid limit 1) AS `name` FROM `notice_list` WHERE `id` = {$id}";
        $res = $this->run($sql);
        if ($res->num_rows == 0) {
            $this->res('访问页面不存在', 5);
        }

        $row = $res->fetch_assoc();
        $this->info['title'] = $row['title'];

        $content = file_get_contents($fileName);
        // 替换../upload/ 为 ./admin/upload/
        $content = str_replace('../upload/', './admin/upload/', $content);
        
        $this->info['content'] = $content;
        $this->info['name'] = $row['name'] . '公告详情';
        $this->info['date'] = $row['date'];
    }
};
$web = new news(1);
$web->init();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>
        <?php echo $web->info['title']; ?>
    </title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
    <style>
        .content a {
            color: #0089ff;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include './header.php'; ?>
    <div class="main">
        <div class="detail">
            <div class="navigation">
                <div class="return">
                    <i class="layui-icon layui-icon-return"></i>
                    <a href="javascript:history.back(-1);">返回</a>
                </div>
                <div class="item">
                    <i class="layui-icon layui-icon-location"></i>
                    <span>当前位置：</span>
                    <a href="./index.php">首页</a>
                    <i class="layui-icon layui-icon-right"></i>
                    <a href="./news.php">新闻公告</a>
                    <i class="layui-icon layui-icon-right"></i>
                    <a href="javascript:;"><?php echo $web->info['name']; ?></a>
                </div>
            </div>
            <div class="title">
                <?php echo $web->info['title']; ?>
            </div>

            <div class="item">
                <i class="layui-icon layui-icon-time"></i>
                <span>发布时间：</span>
                <span><?php echo $web->info['date']; ?></span>
            </div>

            <div class="content">
                <?php echo $web->info['content']; ?>
            </div>
        </div>
    </div>
    <?php include './footer.php'; ?>
</body>
</html>