<?php

/**
 * 获取当前选中的链接
 * @param string $name 链接名称
 * @return string
 */
function getActive($name)
{
    $url = $_SERVER['PHP_SELF'];
    $url = explode('/', $url);
    $url = $url[count($url) - 1];
    return $url == $name ? 'active' : '';
}

function noticeHtml($show = true, $text = '')
{

    $text = str_replace(' ', '&nbsp;', $text);
    $text = preg_replace('/\b(https?:\/\/[\w\-\.\/]+)\b/', '<a href="$1" target="_blank">$1</a>', $text);
    if ($show) {
        return '<div class="notice"><i class="layui-icon layui-icon-about"></i> ' . $text . '<i class="layui-icon layui-icon-close"></i></div><div class="fill"></div>';
    }
    return '';
}
?>
<?php echo noticeHtml($web->sys['notice_state'], $web->sys['notice_text']); ?>
<div class="header">
    <div class="main">
        <div class="box">
            <a href="index.php">
                <div class="logo">
                    <img src="./images/logo.png?v=<?php echo $web->v; ?>" />
                    <div class="text">
                        <div class="title"><?php echo $web->sys['title']; ?></div>
                        <div class="desc"><?php echo $web->sys['Keywords']; ?></div>
                    </div>
                </div>
            </a>
            <div class="nav">
                <a href="index.php" class="<?php echo getActive('index.php'); ?>">
                    <span>首页</span>
                    <span class="en">HOME</span>
                </a>
                <a href="price.php" class="<?php echo getActive('price.php'); ?>">
                    <span>斯塔克交易费用</span>
                    <span class="en">Starknet Gas</span>
                </a>
                <a href="news.php?cid=9" class="<?php echo getActive('news.php'); ?>">
                    <span>斯塔克新闻</span>
                    <span class="en">Starknet NEWS</span>
                </a>
                <a href="app.php" class="<?php echo getActive('app.php'); ?>">
                    <span>斯塔克生态Dapp</span>
                    <span class="en">Starknet Dapp</span>
                </a>
                <a href="https://community.starknet.io/" class="<?php echo getActive('url.php'); ?>" target="_blank">
                    <span>Starknet论坛</span>
                    <span class="en">Starknet Community</span>
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    // 关闭公告
    (() => {
        layui.$('.notice .layui-icon-close').click(function() {
            layui.$('.notice').hide();
            layui.$('.fill').hide();
        })
    })();
</script>