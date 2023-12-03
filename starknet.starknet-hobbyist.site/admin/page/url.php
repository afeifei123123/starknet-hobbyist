<?php
include '../php/api.php';
$api = new _api();
$u = $api->is('url');
$url = $u != '' ? base64_decode($u) : "/";
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>跳转网站</title>
		<meta name="renderer" content="webkit" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<style>
			* {
				margin: 0;
				padding: 0;
			}

			html,
			body {
				width: 100%;
				height: 100%;
			}

			body {
				display: flex;
				background-color: #F2F2F2;
			}

			.main {
				width: 600px;
				max-width: 620px;
				background-color: #FFFFFF;
				border-radius: 5px;
				margin: auto;
				margin-top: 5%;
				padding: 40px 0px;
			}

			.title {
				color: #333333;
				font-size: 22px;
				text-align: center;
			}

			.tips {
				font-size: 16px;
				color: #888888;
				margin-top: 8px;
				text-align: center;
			}

			.url {
				width: 90%;
				background-color: #d6ebff;
				color: #1E90FF;
				line-height: 25px;
				padding: 5px 10px;
				border-radius: 4px;
				margin: 20px auto;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
				word-break: break-all;
			}

			.open {
				background-color: transparent;
				color: #1E90FF;
				border: 1px solid #1E90FF;
				text-decoration: none;
				display: block;
				margin: auto;
				width: 100px;
				height: 40px;
				text-align: center;
				line-height: 40px;
				border-radius: 30px;
			}
		</style>
	</head>
	<body>
		<div class="main">
			<div class="title">即将跳转到外部网站</div>
			<div class="tips">安全性未知，是否继续</div>
			<div class="url"><?php echo $url;?></div>
			<div>
				<a href="<?php echo $url;?>" class="open">继续前往</a>
			</div>
		</div>
	</body>
</html>
