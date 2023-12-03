<?php
include './admin/php/api.php';
class index extends _api
{
    public $title = 'Starkent Dapp';
    public $id = 0;
    public $count = 0;

    public function init()
    {
        $id = intval($this->is('id'));
        // 如果有id就查询id 没有id就查询最新的一个
        if ($id) {
            $sql = "SELECT * FROM `app_list` WHERE `id` = {$id}";
        } else {
            $sql = "SELECT * FROM `app_list` ORDER BY `indexs`,`id` ASC LIMIT 1";
        }
        $res = $this->conn->query($sql);
        if ($res->num_rows) {
            $row = $res->fetch_assoc();
            $this->title = $row['name'];
            $this->id = $row['id'];
        } else {
            $this->title = '404';
        }
    }
    /**
     * 获取App列表
     * @param int $id 应用ID
     * @param int $page 页码
     * @return string
     */
    public function AppList($id, $page = 1)
    {
        if ($page < 1) $page = 1;
        $limit = 1000;
        $start = ($page - 1) * $limit;
        $count = 0;
        $sql = "SELECT `id`,`name`,`img` FROM `app_list` ORDER BY `indexs`,`id` ASC LIMIT {$start},{$limit}";
        $res = $this->conn->query($sql);
        $html = '';
        while ($row = $res->fetch_assoc()) {
            $html .= '<a href="./app.php?id=' . $row['id'] . '&page=' . $page . '" ' . ($id == $row['id'] ? 'class="active"' : '') . '><img src="' . $row['img'] . '" alt="' . $row['name'] . '" title="' . $row['name'] . '" /><span>' . $row['name'] . '</span></a>';
        }

        // 获取总数
        $sql = "SELECT COUNT(*) AS `count` FROM `app_list`";
        $res = $this->conn->query($sql);
        if ($res->num_rows) {
            $row = $res->fetch_assoc();
            $count = $row['count'];
        }

        $this->count = $count;
        return $html;
    }

    /**
     * 获取文档
     * @param int $id 应用ID
     * @return string
     */
    public function getDoc($id)
    {
        $fileName = './admin/upload/app/' . $id . '.html';
        if (!file_exists($fileName)) {
            return '暂无文档';
        }

        $content = file_get_contents($fileName);
        // 替换../upload/ 为 ./admin/upload/
        $content = str_replace('../upload/', './admin/upload/', $content);
        return $content;
    }
};
$web = new index(1);
$web->init();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>
        <?php echo $web->title; ?>
    </title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="./css/style.css?v=<?php echo $web->v; ?>" />
    <script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
    <style>
        .app>.title {
            font-size: 50px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 100px;
            color: #ffffff;
            position: relative;
            text-shadow: 1px 2px 0px #0032ff;
        }

        .app>.title>.bg {
            position: absolute;
            background: #000000;
            bottom: -10px;
            left: 0;
            z-index: -1;
            background-image: linear-gradient(to left, #7065F3, #0930A6);
            width: 200px;
            height: 5px;
        }

        .app>.desc {
            font-size: 20px;
            color: rgb(160, 165, 204);
            margin-bottom: 50px;
        }

        .app>.content {
            display: flex;
            justify-content: space-between;
        }

        .app>.content>.nav {
            width: 200px;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            background-color: #22244D;
            height: fit-content;
        }

        .app>.content>.nav>.title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .app>.content>.nav>a {
            display: block;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 10px;
            color: rgb(160, 165, 204);
            font-size: 16px;
            font-weight: bold;
        }

        .app>.content>.nav>a.active {
            background: #7065F3;
            color: #ffffff;
        }

        .app>.content>.nav>a:hover {
            color: #7065F3;
        }

        .app>.content>.nav>a.active:hover {
            color: #ffffff;
        }

        .app>.content>.nav>a>img {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .app>.content>.nav>a>span {
            vertical-align: middle;
            display: inline-block;
            width: calc(100% - 40px);
            overflow: hidden;
        }

        .app>.content>.body {
            width: calc(100% - 250px);
            border-radius: 10px;
            padding: 0 20px;
        }

        .app>.content>.body>.title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .app>.content>.body>.desc {
            font-size: 16px;
            color: #ffffff;
            background-color: #22244D;
            padding: 20px;
            border-radius: 10px;
        }

        .app>.content>.body>.desc h1,
        .app>.content>.body>.desc h2,
        .app>.content>.body>.desc h3,
        .app>.content>.body>.desc h4,
        .app>.content>.body>.desc h5,
        .app>.content>.body>.desc h6 {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .page {
            text-align: center;
            padding: 20px;
        }

        .layui-laypage a,
        .layui-laypage span {
            background-color: #22244D;
            border: none;
        }

        .layui-laypage-count {
            color: #ffffff !important;
            padding: 0px 10px !important;
        }

        .layui-laypage a[data-page] {
            color: #ffffff;
        }
        
        .app>.content>.body>.desc a {
            color: #0089ff;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include './header.php'; ?>
    <div class="main">
        <div class="app">
            <div class="title">
                <span>Starkent Dapp</span>
                <div class="bg"></div>
            </div>
            <div class="desc">我们编写了20+个starknet Dapp的详细使用方法，帮助新人快速使用starknet网络.
            We have written detailed instructions for using 20+ Starknet Dapps to help newcomers quickly get started with the Starknet network.If there are new Dapps or new content, we will continue to update</div>
            <div class="content">
                <div class="nav">
                    <div class="title">Starkent Dapp List</div>
                    <?php echo $web->AppList(intval($web->is('id')), intval($web->is('page'))); ?>
                </div>
                <div class="body">
                    <div class="title">
                        <i class="layui-icon layui-icon-next"></i>
                        <?php echo $web->title; ?>
                    </div>
                    <div class="desc">
                        <?php echo $web->getDoc($web->id); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="page">
            <div id="page"></div>
        </div>
    </div>
    <?php include './footer.php'; ?>
</body>
<script>
    // layui.laypage.render({
    //     elem: 'page',
    //     count: <?php echo $web->count;; ?>,
    //     limit: 10,
    //     curr: <?php echo intval($web->is('page')); ?>,
    //     theme: '#0767FF',
    //     layout: ['prev', 'page', 'next', 'count'],
    //     jump: function(obj, first) {
    //         if (!first) {
    //             window.location.href = 'app.php?id=<?php echo intval($web->is('id')); ?>&page=' + obj.curr;
    //         }
    //     }
    // });

    const id = Number('<?php echo intval($web->is('id')); ?>');
    // 如果没有id就给第一个加上active
    if (!id) {
        layui.$('.app .nav>a').eq(0).addClass('active');
    }
</script>

</html>