<?php
include './admin/php/api.php';
class wait extends _api
{

};
$web = new wait(1);
$web->method('');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>
        NTF Track
    </title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
    <style>
       .chief {
           text-align: center;
       }
       
       .desc {
           margin-bottom: 100px;
       }
    </style>
</head>

<body>
    <?php include './header.php'; ?>
    <div class="chief">
        <div class="main">
            <div class="title">NTF Track（cooming soon....）</div>
            <div class="desc">Our next development content:
NTF Tarck
On the NTF aggregator on starknet, we will track and collect data from the Element Market, Unframed, FLEX, Pyramid, and four starknet NFT markets. To provide users of Starknet NFT trading with the most comprehensive NFT data analysis and trading, mainly including real-time and latest floor prices, historical transaction volumes, depth of historical sell orders, ranking of NFT trading volume on the Starknet network, and tracking of Starknet NFT whale trading.</div>
        </div>
    </div>
    <div class="content">
        
    </div>
    <?php include './footer.php'; ?>
</body>
<script>
    
</script>

</html>