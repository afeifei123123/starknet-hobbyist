<?php
include './admin/php/api.php';
class index extends _api
{
    public function _set()
    {
        $sql = $this->getSql('', ['id', 'name', 'icon', 'juris']);
        $id = $this->is('id');
        $sql = $id != "" ? $sql['upd'] : $sql['add'];
        $this->run($sql, false);
    }
};
$web = new index(1);
$web->method('');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>
        <?php echo $web->sys['title']; ?>
    </title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="./css/style.css?v=<?php echo $web->v; ?>" />
    <script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
    <style>
        body {
            background-image: url(./images/bg-01.jpg?v=6);
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .content {
            padding: 50px 0;
        }

        .introduce {
            padding: 50px 0;
        }

        .introduce .title {
            font-size: 50px;
            font-weight: 700;
            margin-bottom: 40px;
            color: #ffffff;
            position: relative;
            display: inline-block;
            margin-top: 30px;
        }

        .introduce .title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 4px;
            background: #ffffff;
        }

        .introduce .desc {
            font-size: 18px;
            color: #ffffff;
            line-height: 1.5;
        }

        .introduce h1.en {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #9387d7;
            position: relative;
            display: inline-block;
            margin-top: 30px;
        }

        .introduce p.en {
            font-size: 16px;
            color: #babae9;
            line-height: 1.5;
            opacity: 0.8;
        }

        #banner {
            float: right;
            margin-top: 30px;
        }

        .layui-carousel {
            background-color: transparent;
        }

        .layui-carousel .layui-carousel-item {
            background-color: transparent;
        }

        #banner img {
            width: 300px;
            height: 300px;
        }

        .word {
            padding: 30px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .word .item {
            margin-bottom: 20px;
            padding: 20px;
            background-color: rgb(45 51 104 / 15%);
            border-radius: 10px;
            -webkit-backdrop-filter: blur(3px);
            backdrop-filter: blur(3px);
        }

        .word .item h1.zh {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #ffffff;
            position: relative;
            display: inline-block;
            margin-top: 30px;
        }

        .word .item h1.zh::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 4px;
            background: #ffffff;
        }

        .word .item p.zh {
            font-size: 16px;
            color: #ffffff;
            line-height: 1.5;
            opacity: 0.8;
        }

        .word .item h1.en {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #9387d7;
            position: relative;
            display: inline-block;
            margin-top: 30px;
        }

        .word .item p.en {
            font-size: 16px;
            color: #ffffff;
            opacity: 0.8;
        }
    </style>

</head>

<body>
    <?php include './header.php'; ?>
    <div class="main">

        <div class="layui-row">
            <div class="layui-col-sm8">
                <div class="introduce">
                    <h1 class="title">什么是 Starknet?</h1>
                    <div class="desc">Starknet 是一个基于 ZK-Rollup 技术的去中心化 L2 协议，ZK-Rollup 技术是一种超安全机制，通过该机制，链下证明者使用的输入不会暴露在区块链上。
                        它基于一种高度可扩展的密码学证明系统，称为 STARK，使 dapp 能够在不损害以太坊的可组合性和安全性的情况下实现无限规模。</div>
                    <h1 class="en"><i class="layui-icon layui-icon-next"></i> What is Starknet?</h1>
                    <p class="en"><i class="layui-icon layui-icon-next"></i> Starknet is a decentralized L2 protocol based on ZK-Rollup technology, which is a super-secure mechanism that ensures that the inputs used by off-chain provers are not exposed on the blockchain.It is based on a highly scalable cryptographic proof system called STARK, enabling dapps to achieve unlimited scale without compromising the composability and security of Ethereum.</p>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-carousel" id="banner">
                    <div carousel-item>
                        <div>
                            <img src="./images/banner-1.jpg?v=3" />
                        </div>
                        <div>
                            <img src="./images/banner-2.jpg?v=3" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm12">
                <div class="word">
                    <div class="item">
                        <h1 class="zh">它是如何运作的?</h1>
                        <p class="zh">考虑到上述类比，现在是时候使用一些术语了。Starknet 是一个无需许可的 Validity-Rollup（也称为“ZK-Rollup”），支持一般计算，目前在生产中作为以太坊上的 L2 网络运行。Starknet 最终的 L1 安全性是通过使用最安全、最具扩展性的密码证明系统STARK来保证的。
                            Starknet 合约（大部分）是用 Cairo 语言编写的 - 一种为 STARK 证明设计的图灵完备编程语言。</p>
                        <h1 class="en">How does it work?</h1>
                        <p class="en">Considering the above analogy, it is time to use some terminology.Starknet is a permissionless Validity-Rollup (also known as a "ZK-Rollup") that supports general computation and currently operates as an L2 network on Ethereum in production.Starknet's ultimate L1 security is guaranteed through the use of the most secure and scalable cryptographic proof system, STARK.The Starknet contract is (mostly) written in Cairo language - a Turing-complete programming language designed for STARK proofs.</p>
                    </div>
                    <div class="item">
                        <h1 class="zh">什么是Cairo语言?</h1>
                        <p class="zh">与以太坊有 Solidity 和 Solana 有 Rust 一样，Starknet 有自己的语言，叫做 Cairo。它是在 Starknet 上编写智能合约的本地语言。</p>
                        <h1 class="en">What is Cairo Language?</h1>
                        <p class="en">
                            Just like Ethereum has Solidity and Solana has Rust, Starknet has its own language called Cairo.It is a native language for writing smart contracts on Starknet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include './footer.php'; ?>
</body>
<script>
    const carousel = layui.carousel;
    // 渲染 - 常规轮播
    carousel.render({
        elem: '#banner',
        width: 'auto',
        height: '300px',
        width: '300px',
        arrow: 'none',
        indicator: 'none'
    });
</script>

</html>