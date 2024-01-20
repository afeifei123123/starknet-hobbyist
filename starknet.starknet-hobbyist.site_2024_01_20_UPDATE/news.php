<?php
include './admin/php/api.php';
class news extends _api
{
    public $title = 'Starknet News';
    public $id = 0;
    public $count = 0;
    /**
     * 获取公告分类
     * @param int|string $id 分类id
     * @return string
     */
    public function newsNav($cid)
    {
        $html = '';
        $sql = "SELECT `id`,`name` FROM `notice_category` WHERE `show` = 1 ORDER BY `indexs`,`id` ASC";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $id = $row['id'];
                $name = $row['name'];
                $class = $cid == $id ? 'active' : '';
                $html .= "<a href='news.php?cid={$id}' class='{$class}' data-id='{$id}'>{$name}</a>";
            }
        }
        return $html;
    }

    /**
     * 获取公告列表
     * @param int|string $cid 分类id
     * @param int|string $page 页码
     * @return string
     */
    public function newsList($cid, $page = 1)
    {
        if ($page < 1) $page = 1;
        $limit = 10;
        $start = ($page - 1) * $limit;
        $count = 0;
        $html = '';
        $sql = "SELECT `id`,`title`,`date`,`is_new`,(SELECT `name` FROM `notice_category` WHERE notice_category.id = notice_list.cid limit 1) AS `name` FROM `notice_list`";
        if ($cid) {
            $sql .= "WHERE `cid` = '{$cid}'";
        }
        $sql .= " ORDER BY `date` DESC LIMIT {$start},{$limit}";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $date = $row['date'];
                $is_new = $row['is_new'] == 1 ? '<span class="layui-badge">new</span>' : '';
                $name = $cid ? '' : '【' . $row['name'] . '】';
                $html .= "<a href='news_detail.php?id={$id}' class='item'>
                            <div class='time'><i class='layui-icon layui-icon-time'></i> {$date}</div>
                            <div class='title'>{$is_new}{$name}{$title}</div>
                        </a>";
            }
        } else {
            $html = '<div class="empty">暂无数据</div>';
        }

        // 获取总数
        $sql = "SELECT COUNT(*) AS `count` FROM `notice_list`";
        if ($cid) {
            $sql .= "WHERE `cid` = '{$cid}'";
        }
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $count = $row['count'];
        }
        $this->count = $count;
        return $html;
    }
};
$web = new news(1);
$web->method('');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>
        新闻公告
    </title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
    <style>
        .page {
            text-align: center;
            margin-top: 20px;
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
    </style>
</head>

<body>
    <?php include './header.php'; ?>
    <div class="chief">
        <div class="main">
            <div class="title">Starknet News</div>
            <div class="desc">Starknet News。</div>
        </div>
    </div>
    <div class="content">
        <div class="main">
            <div class="news">
                <div class="nav">
                    <?php echo $web->newsNav(intval($web->is('cid'))); ?>
                </div>
                <div class="list">
                    <?php echo $web->newsList(intval($web->is('cid')), intval($web->is('page'))); ?>
                </div>
                <div class="page">
                    <div id="page"></div>
                </div>
            </div>
        </div>
    </div>
    <?php include './footer.php'; ?>
</body>
<script>
    layui.laypage.render({
        elem: 'page',
        count: <?php echo $web->count;; ?>,
        limit: 10,
        curr: <?php echo intval($web->is('page')); ?>,
        theme: '#0767FF',
        layout: ['prev', 'page', 'next', 'count'],
        jump: function(obj, first) {
            if (!first) {
                window.location.href = 'news.php?cid=<?php echo intval($web->is('cid')); ?>&page=' + obj.curr;
            }
        }
    });
</script>

</html>