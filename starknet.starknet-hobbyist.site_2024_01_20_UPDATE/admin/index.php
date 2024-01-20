<?php
include 'php/api.php';

/**
 * 说明：后台主要接口
 * 版本: 1.0
 * 修改日期: 2023-01-17
 */
class _admin extends _api
{
    /**
     * 获取用户打开的选项卡
     * @return array
     */
    public function getTab(): array
    {
        $w = "WHERE `user_id` = '{$this->id}' ORDER BY `indexs`,`id` ASC";
        return $this->query('user_tab', $w, 'url,title,nav', false, true)['data'];
    }

    /**
     * 获取用户是否有未读消息
     * @return bool
     */
    public function getDot(): bool
    {
        $sql = "SELECT COUNT(*) AS `count` FROM `user_message` WHERE `user_to` = {$this->id} AND `dot` = 1;";
        $res = $this->run($sql);
        $dot = false;
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $count = intval($row['count']);
            $dot = $count > 0;
        }
        return $dot;
    }

    /**
     * 获取用户未读消息数量
     * @return int
     */
    public function getMsg(): int
    {
        $sql = "SELECT COUNT(*) AS `count` FROM `user_message` WHERE `user_to` = {$this->id} AND `read` = 0;";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            return intval($row['count']);
        }
        return 0;
    }

    /**
     * 获取用户信息
     * @return void
     */
    public function _info(): void
    {
        $this->user['tab'] = $this->getTab();
        $this->user['dot'] = $this->getDot();
        $this->user['msg'] = $this->getMsg();
        if ($this->user['picture'] == '') {
            $u = $this->_getNamePicture($this->user['username']);
            $this->user['picture'] = $u;
            $q = "UPDATE `user_data` SET `picture` = '{$u}' WHERE `id` = {$this->id};";
            $this->run($q);
        }
        $sql = "UPDATE `user_data` SET `state` = '1' WHERE `id` = {$this->id};";
        $this->run($sql);
        $nickname  = $this->user['nickname'];
        if ($nickname != '') {
            $this->user['username'] = $nickname;
            unset($this->user['nickname']);
        }
        $this->res('调试成功', 1, $this->user);
    }

    /**
     * 根据用户名生成头像
     * @param $name
     * @return string
     */
    public function _getNamePicture($name): string
    {
        $file = date("YmdHis") . rand(1000, 9999) . ".png";
        $value =  mb_substr($name, 0, 1, 'utf-8');
        $width = 100;
        $height = 100;
        $image = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($image, 30, 144, 255);
        imagefill($image, 0, 0, $backgroundColor);
        $color = imagecolorallocate($image, 255, 255, 255);
        $font = realpath("fonts/captcha.ttf");
        imagettftext($image, 60, 0, 35, 70, $color, $font, $value);
        imagepng($image, "upload/picture/" . $file);
        return $this->host . "upload/picture/" . $file;
    }

    /**
     * 新增选项卡
     * @return void
     */
    public function _AddTab(): void
    {
        $u = $this->is('url');
        $t = $this->is('title');
        $n = $this->is('nav');
        $sql = "SELECT `id` FROM  `user_tab` WHERE `url` = '{$u}' AND `title`='{$t}' AND `nav`='{$n}' AND `user_id` = '{$this->id}' limit 1;";
        $res = $this->run($sql);
        if ($res->num_rows > 0) $this->res('选项卡已存在', 3);
        $time = time();
        $this->db->add('user_tab', [
            'user_id' => $this->id,
            'url' => $u,
            'title' => $t,
            'nav' => $n,
            'found_date' => $time
        ]);
        $this->res('新增成功', 1);
    }

    /**
     * 修改被选中的选项卡
     * @return void
     */
    public function _SetTab()
    {
        $v = $this->is('value');
        $sql = "UPDATE `user_data` SET `tab_url`= '{$v}' WHERE `id` = {$this->id};";
        $this->run($sql, false);
    }

    /**
     * 获取用户实时消息
     * @return void
     */
    public function _message()
    {
        $sql = "SELECT type,body_type FROM `user_message` WHERE `user_to` = {$this->id} AND `client` = 0;";
        $res = $this->run($sql);
        if ($res->num_rows == 0) $this->res('暂无消息', 3);
        $row = $res->fetch_assoc();
        $type = intval($row['type']);
        $b = intval($row['body_type']);
        $sql = "UPDATE `user_message` SET `client` = 1 WHERE `user_to` = {$this->id} AND `client` = 0;";
        $res = $this->run($sql);
        $this->res('有新消息', 1, ['type' => $type, 'body' => $b, 'count' => $this->getMsg()]);
    }

    /**
     * 管理员对左侧菜单进行排序
     * @return void
     */
    public function _SortNav()
    {
        $this->ajax(['surface', 'data']);
        $s = $_REQUEST['surface'];
        $d = $_REQUEST['data'];
        $y = 0;
        $w = '';
        foreach ($d as $k) {
            $id = $k['id'];
            $i = $k['indexs'];
            switch ($s) {
                case 'home_card':
                    if (!$this->admin) $this->res('无权限', 3);
                    $w = "`id` = {$id}";
                    break;
                case 'menu_list':
                    $w = "`id` = '{$id}'";
                    break;
                case 'menu_node':
                    $w = "`id` = '{$id}'";
                    break;
                default:
                    $w = "`id` = {$id} AND `user_id` = {$this->id}";
            };
            $sql = "UPDATE `{$s}` SET `indexs` = {$i} WHERE {$w};";
            $res = $this->run($sql);
            if ($res) $y += 1;
        }
        $this->res($y > 0 ? '排序成功' : '排序失败', $y > 0 ? 1 : 3);
    }

    /**
     * 普通用户对选项卡进行排序
     * @return void
     */
    public function _SortTab()
    {
        $this->ajax(['data']);
        $d = $_REQUEST['data'];
        $y = 0;
        $w = '';
        foreach ($d as $k) {
            $id = $k['id'];
            $i = intval($k['indexs']);
            $w = "`url` = '{$id}' AND `user_id` = {$this->id}";
            $sql = "UPDATE `user_tab` SET `indexs` = {$i} WHERE {$w};";
            $res = $this->run($sql);
            if ($res)  $y += 1;
        }
        $this->res($y > 0 ? '排序成功' : '排序失败', $y > 0 ? 1 : 3);
    }

    /**
     * 关闭选项卡
     * @return void
     */
    public function _CloseTab()
    {
        $u = $this->is('url');
        $sql = "DELETE FROM `user_tab` WHERE `user_id` = {$this->id} AND `url` = '{$u}' limit 1;";
        $this->run($sql, false);
    }

    /**
     * 退出登录
     * @return void
     */
    public function _quit()
    {
        $path = '/';
        $time = time() - 3600 * 24;
        setcookie("{$this->token}", '', $time, $path);
        $sql = "UPDATE `user_data` SET `token` = '0',`state` = '0' WHERE `id` = {$this->user['id']};";
        $this->run($sql);
        if ($this->sys['lanq_state'] == '1') $this->sendMessage(1, 0, $this->user['picture'], "{$this->user['username']}-注销登录", '', 1);
        $this->res('退出成功', 1);
    }

    /**
     * 获取验证码
     * @return void
     */
    public function _captcha()
    {
        session_start();
        $h = 60;
        $w = $h * 2.6;
        $img = imagecreatetruecolor($w, $h);
        $bgcolor = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bgcolor);
        $str = '1234567890';
        $font = realpath('./fonts/captcha.ttf');
        if (!file_exists($font)) $this->res("{$font}字体文件不存在", 3);
        $angle = [45, -100];
        $count = 10;
        for ($i = 0; $i < $count; $i++) {
            $text = $str[rand(0, strlen($str) - 1)];
            $color = imagecolorallocate($img, rand(200, 255), rand(100, 255), rand(100, 255));
            imagefttext($img, $h / 3, rand(0, 360), rand(0, $w), rand(0, $h), $color, $font, $text);
        }
        $code = '';
        $num = 4;
        $angle = [-30, -25, -20, -15, 10, 5, 0, 5, 10, 15, 20, 25, 30];
        $index = 0;
        for ($i = 0; $i < $num; $i++) {
            $text = $str[rand(0, strlen($str) - 1)];
            $code .= $text;
            $size = $h / 1.5;
            $index += $w / 6;
            $color = imagecolorallocate($img, rand(0, 200), rand(0, 200), rand(0, 200));
            $x = rand(0, count($angle) - 1);
            imagefttext($img, $size, $angle[$x], $index + $h / 6, $h / 1.3, $color, $font, $text);
        }
        $_SESSION['authcode'] = $code;
        session_write_close();
        $line = 3;
        for ($i = 0; $i < $line; $i++) {
            $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetthickness($img, $h / 30);
            imagearc($img, rand(-$w, $w), rand(-$h, $h), rand(0, $w * 2), rand(0, $h * 2), rand(0, 360), rand(0, 360), $color);
        }
        header('Cache-Control: no-cache');
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }

    /**
     * 用户注册
     * @return void
     */
    public function _register()
    {
        $this->ajax(['username', 'password'], true);
        $u = trim($_REQUEST['username']);
        $p = trim($_REQUEST['password']);
        $s = $this->sys['sms_state'];
        if ($this->sys['register_state'] == '0') $this->res('网站未开启注册', 3);
        $sql = "SELECT id FROM `user_data` WHERE `username` = '{$u}' limit 1";
        $res = $this->run($sql);
        if ($res->num_rows > 0) $this->res('用户名已存在!', 3);
        if ($s == '1') {
            $c = $this->is('smscode');
            $this->isSmsCode($u, $c);
        }

        $this->db->add('user_data', [
            'username' => $u,
            'password' => $p,
            'ip' => $this->ip,
            'roles_id' => $this->sys['default_roles']
        ]);
        $this->res('注册成功', 1);
    }


    /**
     * 登录帐号
     * @return void
     */
    public function _logon()
    {

        if ($this->user) {
            header("Location: /{$this->host}");
            exit;
        }
        $this->form([
            'username' => ['required', 'username'],
            'password' => ['required', 'password'],
            'captcha' => ['captcha'],
        ]);
        $u = trim($_REQUEST['username']);
        $p = trim($_REQUEST['password']);
        if ($this->sys['captcha_state'] == 1) {
            session_start();
            $c = $this->is('captcha');
            $s = $_SESSION['authcode'];
            session_write_close();
            if (strtolower($c) !== strtolower($s)) $this->res('验证码不正确!', 3);
        }
        $t = date('mdHis');
        $sql = "SELECT id,blacklist,picture FROM `user_data` WHERE `username` = '{$u}' AND`password` = '{$p}' limit 1";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $id = $row['id'];
            $b = $row['blacklist'];
            if ($b == '1') $this->res('账号已被管理员拉黑，禁止登录', 5);
            $path = '/';
            $time = time() + 3600 * 24;
            $tk = md5($u . $p . $t);
            setcookie("{$this->token}", $tk, $time, $path);
            $sql = "UPDATE `user_data` SET `token` = '{$tk}' WHERE `id` = {$id};";
            $res = $this->run($sql);
            $this->id = $id;
            if ($this->sys['lanq_state'] == '1') $this->sendMessage(1, 0, $row['picture'], "{$u}-登录平台", '无', 0);
            $type = $this->is_mobile() ? '1' : '0';
            $os = $this->getOS();
            $area = $this->GetArea($this->ip);
            $this->db->add('logon_log', [
                'user_id' => $id,
                'type' => $type,
                'ip' => $this->ip,
                'os' => $os,
                'area' => $area
            ]);
            $this->res('登录成功', 1);
        }
        $this->res('用户名或者密码错误!', 5);
    }

    /**
     * 获取IP地址
     * @param string $ip
     * @return string
     */
    public function GetArea($ip)
    {
        $type = 0;
        if ($type == 0) {
            $url = "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query={$ip}&resource_id=6006";
            $str = file_get_contents($url);
            $res = json_decode(mb_convert_encoding($str, 'UTF-8', ['ASCII', 'UTF-8', 'GB2312', 'GBK']), true);
            if ($res['status'] == 0) {
                if (!isset($res['data'][0]['location'])) {
                    return '测试环境';
                }
                return $res['data'][0]['location'];
            }
        }
        if ($type == 1) {
            $url = "https://www.ip138.com/iplookup.asp?ip={$ip}&action=2";
            $res = file_get_contents($url);
            $html = iconv("gb2312", "utf-8", $res);
            $str = explode("var ip_result = ", $html)[1];
            $str = explode(";", $str)[0];
            $json = json_decode($str, true);
            return $json['ASN归属地'];
        }
        return '-';
    }

    /**
     * 用户找回密码
     * @return void
     */
    public function _retpawd()
    {
        if ($this->sys['retpawd_state'] == '0') $this->res('网站未开启找回密码', 3);
        if ($this->sys['sms_state'] == '0') $this->res('网站未开启短信功能，无法找回密码', 3);
        $this->ajax(['username', 'smscode', 'password'], true);
        $u = $_REQUEST['username'];
        $s = $_REQUEST['smscode'];
        $p = $_REQUEST['password'];
        $this->isSmsCode($u, $s);
        $sql = "UPDATE `user_data` SET `password` = '{$p}' WHERE `username` = {$u};";
        $res = $this->run($sql);
        $n = $this->conn->affected_rows;
        $this->res($n >= 1 ? '密码找回成功' : '旧密码与新密码不能相同', $n >= 1 ? 1 : 3);
    }

    /**
     * @return bool
     */
    public function is_mobile(): bool
    {
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
        $regex_match .= ")/i";
        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    /**
     * 获取操作系统
     * @return string
     */
    public function getOS()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $os = 'MSC';
        if (preg_match('/win/i', $agent) && strpos($agent, '95')) $os = 'Windows 95';
        if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) $os = 'Windows ME';
        if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) $os = 'Windows 98';
        if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) $os = 'Windows NT';
        if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) $os = 'Windows Vista';
        if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) $os = 'Windows 7';
        if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) $os = 'Windows 8';
        if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) $os = 'Windows 10';
        if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) $os = 'Windows XP';
        if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) $os = 'Windows 2000';
        if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) $os = 'Windows 32';
        if (preg_match('/linux/i', $agent)) $os = 'Linux';
        if (preg_match('/unix/i', $agent)) $os = 'Unix';
        if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) $os = 'SunOS';
        if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) $os = 'IBM OS/2';
        if (preg_match('/mac/i', $agent) && preg_match('/PC/i', $agent)) $os = 'Macintosh';
        if (preg_match('/powerpc/i', $agent)) $os = 'PowerPC';
        if (preg_match('/aix/i', $agent)) $os = 'AIX';
        if (preg_match('/hpux/i', $agent)) $os = 'HPUX';
        if (preg_match('/netbsd/i', $agent)) $os = 'NetBSD';
        if (preg_match('/bsd/i', $agent)) $os = 'BSD';
        if (preg_match('/osf1/i', $agent)) $os = 'OSF1';
        if (preg_match('/irix/i', $agent)) $os = 'IRIX';
        if (preg_match('/freebsd/i', $agent)) $os = 'FreeBSD';
        if (preg_match('/teleport/i', $agent)) $os = 'teleport';
        if (preg_match('/flashget/i', $agent)) $os = 'flashget';
        if (preg_match('/webzip/i', $agent)) $os = 'webzip';
        if (preg_match('/offline/i', $agent)) $os = 'offline';
        if (strpos($agent, 'iphone')) $os = 'iphone';
        if (strpos($agent, 'ipad')) $os = 'ipad';
        if (strpos($agent, 'android')) $os = 'android';
        if (stripos($agent, "samsung") !== false || stripos($agent, "Galaxy") !== false || str_contains($agent, "GT-") || strpos($agent, "SCH-") !== false || strpos($agent, "SM-") !== false) $os = 'android ->三星';
        if (stripos($agent, "huawei") !== false || stripos($agent, "Honor") !== false || stripos($agent, "H60-") !== false || stripos($agent, "H30-") !== false) $os = 'android ->华为';
        if (stripos($agent, "lenovo") !== false) $os = 'android ->联想';
        if (str_contains($agent, "mi-one") || str_contains($agent, "MI 1S") || str_contains($agent, "MI 2") || str_contains($agent, "MI 3") || strpos($agent, "MI 4") !== false || strpos($agent, "MI-4") !== false) $os = 'android ->小米';
        if (str_contains($agent, "hm note") || str_contains($agent, "HM201")) $os = 'android ->红米';
        if (stripos($agent, "coolpad") !== false || str_contains($agent, "8190Q") || str_contains($agent, "5910")) $os = 'android ->酷派';
        if (stripos($agent, "zte") !== false || stripos($agent, "X9180") !== false || stripos($agent, "N9180") !== false || stripos($agent, "U9180") !== false) $os = 'android ->中兴';
        if (stripos($agent, "oppo") !== false || str_contains($agent, "X9007") || str_contains($agent, "X907") || strpos($agent, "X909") !== false || strpos($agent, "R831S") !== false || strpos($agent, "R827T") !== false || strpos($agent, "R821T") !== false || strpos($agent, "R811") !== false || strpos($agent, "R2017") !== false) $os = 'android ->OPPO';
        if (str_contains($agent, "htc") || stripos($agent, "Desire") !== false) $os = 'android ->HTC';
        if (stripos($agent, "vivo") !== false) $os = 'android ->vivo';
        if (stripos($agent, "k-touch") !== false) $os = 'android ->天语';
        if (stripos($agent, "Nubia") !== false || stripos($agent, "NX50") !== false || stripos($agent, "NX40") !== false) $os = 'android ->努比亚';
        if (str_contains($agent, "m045") || str_contains($agent, "M032") || str_contains($agent, "M355")) $os = 'android ->魅族';
        if (stripos($agent, "doov") !== false) $os = 'android ->朵唯';
        if (stripos($agent, "gfive") !== false) $os = 'android ->基伍';
        if (stripos($agent, "gionee") !== false || str_contains($agent, "GN")) $os = 'android ->金立';
        if (stripos($agent, "hs-u") !== false || stripos($agent, "HS-E") !== false) $os = 'android ->海信';
        if (stripos($agent, "nokia") !== false) $os = 'android ->诺基亚';
        return $os;
    }

    /**
     * 初始化网站
     * @return void
     */
    public function _init(): void
    {
        $c = $this->c(1, false, true);
        if ($c) $this->res('网站已正常运行，禁止再次初始化', 3);
        $this->ajax(['servername', 'username', 'password', 'dbname', 'port'], false);
        $d = ['servername' => $_REQUEST['servername'], 'username' => $_REQUEST['username'], 'password' => $_REQUEST['password'], 'dbname' => $_REQUEST['dbname'], 'port' => $_REQUEST['port']];
        $c = $this->c(1, $d, true);
        if (!$c) $this->res('无法连接到您设置的数据库', 3);
        $f = 'file/mysql.sql';
        if (!file_exists($f)) $this->res("缺少数据库文件：{$f}", 3);
        $b = file_get_contents($f);
        $a = explode(";\r\n", $b);
        $s = 0;
        foreach ($a as $k) {
            $not = strpos($k, '/*');
            if (!$not) {
                if ($k != '' || $k != '\n' || $k != '\r\n') {
                    $q = $c->query($k);
                    if ($q) $s += 1;
                }
            }
        }
        if ($s > 0) {
            $f = "<?php\n\$mysql =" . str_replace([':', '{', '}', ','], ['=>', '[', ']', ",\n"], json_encode($d)) . ";\n";
            file_put_contents('php/user.php', $f);
        }
        $this->curl($this->server . '/php/api.php?eventType=deploy');
        $this->res($s == 0 ? '网站初始化失败' : '网站初始化成功', $s == 0 ? 5 : 1);
    }

    /**
     * 删除左侧菜单
     * @return void
     */
    public function _MenuDel(): void
    {
        $this->ajax(['id']);
        $id = intval($_REQUEST["id"]);
        $sql = "DELETE FROM `menu_list` WHERE `id` = {$id};";
        $this->run($sql);
        $sql = "DELETE FROM `menu_node` WHERE `menu_id` = {$id};";
        $this->run($sql, false);
    }

    /**
     * 删除左侧菜单栏目
     * @return void
     */
    public function _ItemDel(): void
    {
        $this->ajax(['id']);
        $id = intval($_REQUEST["id"]);
        $sql = "DELETE FROM `menu_node` WHERE `id` = {$id};";
        $this->run($sql, false);
    }

    /**
     * 修改左侧默认折叠状态
     * @return void
     */
    public function _stateMenu(): void
    {
        $this->ajax(['id', 'state']);
        $id = intval($_REQUEST["id"]);
        $state = intval($_REQUEST["state"]);
        $sql = "UPDATE `menu_list` SET `state` = '{$state}' WHERE `id` = {$id};";
        $this->run($sql, false);
    }

    /**
     * 离开页面就离线
     * @return void
     */
    public function _leave(): void
    {
        $q = "UPDATE `user_data` SET `state` = '0' WHERE `id` = {$this->id};";
        $this->run($q, false);
    }

    /**
     * 强制要求网站进行授权校验
     * @return void
     */
    public function _stop(): void
    {
        $sql = "SELECT `value` FROM `system_setup` WHERE `name` = 'system';";
        $res = $this->run($sql);
        if ($res->num_rows == 0) {
            $this->res('error', 3);
        }
        $row = $res->fetch_assoc();
        $data = json_decode($row['value'], true);
        $data['run_state'] = '0';
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE `system_setup` SET `value` = '{$data}' WHERE `name` = 'system';";
        $this->run($sql, false);
    }

    /**
     * 关闭网站授权校验
     * @return void
     */
    public function _start(): void
    {
        $conn = $this->c(2, false, true);
        $this->conn = $conn;
        $sql = "SELECT `value` FROM `system_setup` WHERE `name` = 'system';";
        $res = $this->run($sql);
        if ($res->num_rows == 0) {
            $this->res('error', 3);
        }
        $row = $res->fetch_assoc();
        $data = json_decode($row['value'], true);
        $data['run_state'] = '1';
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE `system_setup` SET `value` = '{$data}' WHERE `name` = 'system';";
        $this->run($sql, false);
    }
}

if (isset($_REQUEST['method'])) {
    $method = $_REQUEST['method'];
    switch ($method) {
        case 'admin':
            $web = new _admin(2, 'username,id,tab_url,picture,nickname');
            $web->_info();
            break;
        case 'AddTab':
            $web = new _admin(2);
            $web->_AddTab();
            break;
        case 'SetTab':
            $web = new _admin(2);
            $web->_SetTab();
            break;
        case 'message':
            $web = new _admin(2);
            $web->_message();
            break;
        case 'SortNav':
            $web = new _admin(2, '*', false, true);
            $web->_SortNav();
            break;
        case 'SortTab':
            $web = new _admin(2);
            $web->_SortTab();
            break;
        case 'CloseTab':
            $web = new _admin(2);
            $web->_CloseTab();
            break;
        case 'quit':
            $web = new _admin(2);
            $web->_quit();
            break;
        case 'captcha':
            $web = new _admin(1);
            $web->_captcha();
            break;
        case 'logon':
            $web = new _admin(2, '*', true);
            $web->_logon();
            break;
        case 'sendSmsCode':
            $web = new _admin(1);
            $web->_sendSmsCode();
            break;
        case 'register':
            $web = new _admin(1);
            $web->_register();
            break;
        case 'retpawd':
            $web = new _admin(1);
            $web->_retpawd();
            break;
        case 'upload':
            $web = new _admin(2);
            $web->_upload();
            break;
        case 'init':
            $web = new _admin(0);
            $web->_init();
            break;
        case 'MenuDel':
            $web = new _admin(2, '*', false, true);
            $web->_MenuDel();
            break;
        case 'ItemDel':
            $web = new _admin(2, '*', false, true);
            $web->_ItemDel();
            break;
        case 'stateMenu':
            $web = new _admin(2, '*', false, true);
            $web->_stateMenu();
            break;
        case 'delFile':
            $web = new _admin(2);
            $web->delFile();
            break;
        case 'leave':
            $web = new _admin(2);
            $web->_leave();
            break;
        case 'stop':
            $web = new _admin(1);
            $web->_stop();
            break;
        case 'start':
            $web = new _admin(0);
            $web->_start();
            break;
        default:
            $web = new _api();
            $web->res('方法不存在', 3);
            break;
    }
}

$web = new _api();
$conn = $web->c(2, false, true);
if (!$conn || isset($_GET['init'])) $web->includePage('page/init.php');
$web->conn = $conn;
$logon = $web->u(2, 'id', true);
if (!$logon) $web->includePage('page/logon.php');
$web->includePage("page/admin.php");
