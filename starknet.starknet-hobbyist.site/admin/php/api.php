<?php

/**
 * 说明：公共接口
 * 版本: 1.0
 * 修改日期: 2023-01-17
 */

/**
 * 数据库类
 */
class _db
{
    var $db = false;
    public function __construct($e, $a)
    {
        if ($e == 0) return false;
        $this->db = $a->conn;
    }

    /**
     * 新增数据
     * @param string $table 表名
     * @param array $data 数据
     * @return int
     */
    public function add($table, $data)
    {
        $sql = "INSERT INTO `{$table}` (";
        $sql .= implode(',', array_map(function ($v) {
            return "`{$v}`";
        }, array_keys($data)));
        $sql .= ") VALUES (";
        $sql .= implode(',', array_fill(0, count($data), '?'));
        $sql .= ")";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
        $stmt->execute();
        $stmt->close();
        return $this->db->insert_id;
    }

    /**
     * 删除数据
     * @param string $table 表名
     * @param string $where 条件
     * @return bool
     */
    public function del($table, $where)
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stmt->close();
        return $this->db->affected_rows;
    }

    /**
     * 修改数据
     * @param string $table 表名
     * @param array $data 数据
     * @param string $where 条件
     * @return bool
     */
    public function upd($table, $data, $where)
    {
        $sql = "UPDATE `{$table}` SET ";
        $sql .= implode('=?,', array_keys($data)) . '=?';
        $sql .= " WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
        $stmt->execute();
        $stmt->close();
        return $this->db->affected_rows;
    }

    /**
     * 查询数据
     * @param string $table 表名
     * @param string $field 字段
     * @param string $where 条件
     * @param string $order 排序
     * @param string $limit 限制
     * @return array|bool
     */
    public function query($table, $field = '*', $where = '1=1', $order = '', $limit = '')
    {
        $sql = "SELECT {$field} FROM `{$table}` WHERE {$where}";
        if ($order) $sql .= " ORDER BY {$order}";
        if ($limit) $sql .= " LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

/**
 * 公共接口类
 */
class _api
{


    var $token = 'SwithToken'; //token名称
    var $server = 'https://plug.20ps.cn/'; //服务器地址
    var $host = ''; //当前页面地址
    var $ip; //用户ip
    var $conn = false; //数据库对象
    var $db = false; //封装数据库操作
    var $sys = false; //系统信息
    var $v = 1; //版本号
    var $user = false; //用户信息
    var $id = 0; //默认用户id
    var $admin = false; //是否为管理员
    var $info = false; //编辑页面信息
    var $sqls = []; //sql语句

    /**
     * 实例化程序
     * @param int $e 0:不连接数据库 1:连接数据库 2:连接数据库并验证用户
     * @param string $f 需要获取的用户信息(字段)
     * @param bool $s 如果未登录是否跳转到登录页面
     * @param bool $j 是否验证权限
     */
    public function __construct($e = 0, $f = '*', $s = false, $j = false)
    {
        $t = $this;
        $t->ip = $_SERVER['REMOTE_ADDR'];
        $t->host = explode('?', '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])[0];
        $t->conn = $t->c($e);
        $t->db = new _db($e, $t);
        $t->s($e);
        $t->u($e, $f, $s);
        $t->r($e); //记录访问日志
        $t->j($j); //判断有无权限
        $t->b(); //判断有无安装插件  
    }

    /**
     * 连接到数据库
     * @param int $e 0:不连接数据库 1|2:连接数据库
     * @param array $d 数据库配置(非必填，默认读取user.php)
     * @param bool $r 诺连接失败是否返回结果
     * @return bool|mysqli
     */
    public function c($e, $d = false, $r = false)
    {
        if ($e == 0) return false;
        $t = $this;
        include $t->cc();
        $db = $mysql;
        $s = !$d ? $db['servername'] : $d['servername'];
        $u = !$d ? $db['username'] : $d['username'];
        $p = !$d ? $db['password'] : $d['password'];
        $n = !$d ? $db['dbname'] : $d['dbname'];
        $b = !$d ? $db['port'] : $d['port'];
        $c = new mysqli($s, $u, $p, $n, $b);
        if ($c->connect_error) return !$r ? $t->res('无法连接数据库', 5) : false;
        $c->set_charset('utf8');
        return $c;
    }

    /**
     * 读取数据库配置文件
     * @return array|bool|string
     */
    public function cc()
    {
        $u = 'user.php';
        if (file_exists($u)) return $u;
        if (file_exists("php/{$u}")) return "php/{$u}";
        if (file_exists("../php/{$u}")) return "../php/{$u}";
        if (file_exists("../../php/{$u}")) return "../../php/{$u}";
        $this->res('数据库配置文件不存在', 3);
    }

    /**
     * 关闭数据库连接
     * @return bool
     */
    public function q()
    {
        $t = $this;
        return isset($t->conn) ? (!$t->conn ? true : $t->conn->close()) : true;
    }

    /**
     * 获取系统信息
     * @param int $t 0:不获取 1|2:获取
     * @param string $f 需要获取的字段
     * @return bool
     */
    public function s($t = 0, $f = 'value')
    {
        $s = $this;
        $p = '/page/';
        if (strstr($s->host, $p)) $s->host = explode($p, $s->host)[0] . '/';
        if ($t == 0) {
            date_default_timezone_set('Asia/Shanghai');
            $s->v = 001;
            return false;
        }
        $q = "SELECT {$f} FROM `system_setup` WHERE `name` = 'system' LIMIT 1";
        $r = $s->run($q);
        if ($r->num_rows == 0) $s->res('缺少system_setup表', 5);
        $m = json_decode($r->fetch_assoc()['value'], true);
        if (isset($m['run_state']) && $m['run_state'] == '0') {
            $u = $s->server . '/php/api.php?eventType=app';
            $res = $s->curl($u);
            if ($res['icon'] != 1) {
                $s->res($res['code'], $res['icon']);
            }
        }
        $s->v = $m['version'] ?: time();
        isset($m['timezone']) && date_default_timezone_set($m['timezone']);
        $s->sys = $m;
    }

    /**
     * 获取用户信息
     * @param int $t 0|1:不获取 2:获取
     * @param string $f 需要获取的字段
     * @param bool $r 如果未登录是否返回结果
     * @return bool
     */
    public function u($t = 0, $f = '*', $r = false)
    {
        $s = $this;
        if ($t < 2) {
            $s->user = false;
            $s->id = false;
            $s->admin = false;
            return false;
        }
        if (!isset($_COOKIE[$s->token])) return !$r ? $s->l() : false;
        if (!strstr($f, '*') && !strstr($f, 'admin')) $f = $f . ',`admin`';
        if (!strstr($f, '*') && !strstr($f, 'roles_id')) $f = $f . ',`roles_id`';
        $k = $s->verify($_COOKIE[$s->token], 'token');
        $sql = "SELECT {$f} FROM `user_data` WHERE `token` = '{$k}' limit 1";
        $res = $s->run($sql);
        if ($res->num_rows > 0) {
            $s->user = $res->fetch_assoc();
            $s->id = isset($s->user['id']) ? $s->user['id'] : false;
            $s->admin = isset($s->user['admin']) ? ($s->user['admin'] == '1' ? true : false) : false;
            $b = isset($s->user['blacklist']) ? $s->user['blacklist'] : '';
            if ($b == '1') {
                $s->e('您的账号已被封禁', '请联系管理员');
            }

            if (isset($s->sys['server_state'])) {
                $i = $s->sys['server_state'];
                if ($i == '0' && !$s->admin && $s->id > 0) {
                    $s->e('服务器维护中', '请稍后再试');
                }
            }
            return $s->user;
        }
        return !$r ? $s->l() : false;
    }

    /**
     * 跳转到登录页面
     * @return void
     */
    public function l()
    {
        header("location: {$this->host}");
        exit();
    }

    /**
     * 显示错误页面
     * @param string $t 错误信息
     * @param string $r 错误描述
     * @return void
     */
    public function e($t = '', $r = '')
    {
        header('Content-type:text/html;charset=utf-8');
        $f = 'error.php';
        if (is_file($f)) include $f;
        $f = 'page/error.php';
        if (is_file($f)) include $f;
        exit();
    }

    /**
     * 验证用户权限
     * @param string $s 是否需要验证(默认false)
     * @return bool
     */
    public function j($s = false)
    {
        $i = $this;
        if (!$s) return true;
        if ($i->admin) return true;
        if (isset($i->sys['juris_state']) && $i->sys['juris_state'] == 0) return true;
        $f = $_SERVER['PHP_SELF'];
        $t = $i->is('method', false);
        if (!$t) $t = $i->is('method', false);
        if (!$t) $t = $f;
        $t->db->add('juris_log', [
            'path' => $f,
            'method' => $t,
            'ip' => $i->ip,
            'user_id' => $this->id
        ]);
        $q = "SELECT `id` FROM  `juris_data` WHERE `path` = '{$f}' AND `method` = '{$t}' limit 1;";
        $res = $i->run($q);
        if ($res->num_rows == 0) $i->_ja($f, $t, true);
        $rw = $res->fetch_assoc();
        $q = "SELECT `id` FROM  `juris_list` WHERE `roles_id` = '{$i->user['roles_id']}' AND `juris_id` = '{$rw['id']}' limit 1;";
        $r = $i->run($q);
        if ($r->num_rows == 0) $i->_ja($f, $t);
        return true;
    }

    /**
     * 无权限访问日志
     * @param string $p 访问路径
     * @param string $m 请求方法
     * @param bool $s 是否添加到数据库
     * @return void
     */
    public function _ja($p, $m, $s = false)
    {
        $i = $this;
        if ($s) {
            $i->db->add('juris_data', [
                'path' => $p,
                'method' => $m,
                'name' => $m
            ]);
        }
        if ($p == $m) {
            $t = '无权限访问';
            $r = '请联系管理员授权角色访问权限';
            $i = ['访问路径：' . $p, '请求方法：' . $m, '访问时间：' . date('Y-m-d H:i:s'), '访问IP：' . $i->ip];
            include 'error.php';
            exit();
        };
        $i->res("无权限访问！<br />路径：{$p}<br/>方法：{$m}", 3);
    }


    /**
     * 查询是否有可执行的插件(默认开启)
     * @return void
     */
    public function b()
    {
        if (isset($this->sys['plug_state']) && $this->sys['plug_state'] == '0') return false;
        $e = $this->is('method');
        $d = "bin/{$e}/method.php";
        if (file_exists($d)) return include $d;;
        if (is_dir('php') && is_dir('page')) return false;
        $d = "../bin/{$e}/method.php";
        if (file_exists($d)) return include $d;;
    }

    /**
     * 生成请求日志
     * @param int $e 小于1不记录
     * @return void
     */
    public function r($e = 0)
    {
        if ($e < 1) return;
        if (isset($this->sys['request_state']) && $this->sys['request_state'] == '0') return;
        $u = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] : '';
        if (!$u) return;
        if (strpos($u, 'request_log') !== false) return;
        $t = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        $g = json_encode($_GET, JSON_UNESCAPED_UNICODE);
        $p = json_encode($_POST, JSON_UNESCAPED_UNICODE);
        $id = isset($this->id) ? $this->id : '0';
        $this->db->add('request_log', [
            'url' => $u,
            'type' => $t,
            'get' => $g,
            'post' => $p,
            'ip' => $this->ip,
            'user_id' => $id
        ]);
    }

    /**
     * 验证GET/POST参数
     * @param $d array 需要验证的参数 ['参数名:类型','参数名:类型']
     * @param $r bool 是否根据字段名验证
     * @param $z bool 是否转义
     * @return array
     */
    public function ajax($d = [], $r = false, $z = true)
    {
        $t = $this;
        foreach ($d as $k) {
            $i = explode(':', $k);
            $a = explode('=', $k);
            if (!isset($_REQUEST[$i[0]]) && count($a) == 1) $t->res("缺少请求参数:{$k}", 5);
            $q = count($a) == 2 ? $a[1] : $_REQUEST[$i[0]];
            $s = is_string($q) ? $t->verify($q, count($i) == 2 ? $i[1] : ($r ? $k : '')) : $q;
            if (is_string($q) && $z) $_REQUEST[count($a) == 2 ? $a[0] : $i[0]] = htmlspecialchars(addslashes($s));
        }
        return $d;
    }

    /**
     * 验证文本
     * @param string $s 需要验证的文本
     * @param string $t 验证类型
     * @return string
     */
    public function verify($s, $t = '')
    {
        $i = $this;
        if ($s == '') return $s;
        $d = [
            ['数字', 'number', '/^[0-9]*$/'],
            ['字母', 'letter', '/^[a-zA-Z]*$/'],
            ['网址', 'url', '/^((https|http|ftp|rtsp|mms)?:\/\/)[^\s]+/'],
            ['纯中文', 'zh', '/^[\x{4e00}-\x{9fa5}]+$/u'],
            ['邮箱', 'email', '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/'],
            ['手机号', 'tel', '/^1[0-9]{10}$/'],
            ['数字或者字母', 'username', '/^\w{3,20}$/'],
            ['数字或者字母', 'password', '/^[a-zA-Z0-9]{6,16}$/'],
            ['TOKEN', 'token', '/^[a-f0-9]{32}$/'],
            ['version', 'version', '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}$/'],
        ];
        $b = isset($i->sys['ban_word']) ? $i->sys['ban_word'] : '';
        if ($b) {
            $w = '/' . $i->sys['ban_word'] . '/';
            if ($w && preg_match($w, $s, $r)) $i->res("“" . str_replace($r[0], "<b style='color:red;'>{$r[0]}</b>", $s) . "”包含违禁词", 3);
        }
        foreach ($d as $k) if ($t == $k[1]) return preg_match($k[2], $s) ? $s : $i->res("<b style='color:red;'>{$s}</b> 格式必须为“<b>{$k[0]}</b>”！", 3);
        $u = isset($i->sys['unless_state']) ? $i->sys['unless_state'] : false;
        if (!$u && $u == '0') return $s;
        $is = preg_match_all('/((ht|f)tps?):\/\/[\w\-]+(\.[\w\-]+)+([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?/', $s, $arr);
        if (!$is) return $s;
        foreach ($arr[0] as $key) {
            $l = $_SERVER['SERVER_NAME'];
            if (!strstr($key, $l)) {
                $h = $i->host . 'page/url.php?url=' . base64_encode($key);
                $s = str_replace($key, $h, $s);
            }
        }
        return $s;
    }

    /**
     * @param string|array $msg 消息
     * @param int|string $code 状态码
     * @param mixed $data 数据
     * @return void
     */
    public function res(string|array $msg = '操作成功', int|string $code = 1, mixed $data = false): void
    {
        header('Content-Type:application/json; charset=utf-8');
        $json = is_string($msg) ? ['code' => intval($code), 'msg' => $msg, 'data' => (!$data ? [] : $data), 'time' => date('Y-m-d H:i:s')] : $msg;
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取请求参数
     * @param string $n 参数名
     * @param string $v 默认值
     * @param bool $t 是否验证必填
     * @param bool $z 是否转义
     * @return string|array
     */
    public function is($n, $v = '', $t = true, $z = true)
    {
        $i  = isset($_REQUEST[$n]) ? true : false;
        $_REQUEST[$n] = $i ? $_REQUEST[$n] : $v;
        if ($t) {
            $this->ajax([$n], '', $z);
            $r = $_REQUEST[$n];
            if (!$i) unset($_REQUEST[$n]);
            return $r;
        }
        $r = $z ? htmlspecialchars(addslashes($_REQUEST[$n])) : $_REQUEST[$n];
        if (!$i) unset($_REQUEST[$n]);
        return $r;
    }

    /**
     * 发起curl请求
     * @param string $u 请求地址
     * @param array $d body参数(非必填)
     * @param string $h header参数(非必填)
     * @param bool $j 是否将返回结果转为数组(非必填，默认true)
     * @param string $t 请求方式 POST|GET(非必填，默认POST)
     * @return string|array
     */
    public function curl($u, $d = [], $h = '', $j = true, $t = 'POST')
    {
        $a = [];
        $h = explode("\n", $h);
        foreach ($h as $v) {
            $v = trim($v);
            if (empty($v)) continue;
            if (strpos($v, ':') === 0) continue;
            if (strpos($v, 'accept-encoding') !== false) continue;
            $a[] = str_replace('        ', '', $v);
        }
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $u);
        curl_setopt($c, CURLOPT_HEADER, 0);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_TIMEOUT, 1000);
        curl_setopt($c, CURLOPT_HTTPHEADER, $a);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        if ($t == 'POST') curl_setopt($c, CURLOPT_POST, 1);
        if ($t == 'POST') curl_setopt($c, CURLOPT_POSTFIELDS, $d);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_MAXREDIRS, 10);
        curl_setopt($c, CURLOPT_AUTOREFERER, 1);
        $r = curl_exec($c);
        if (curl_error($c)) return $j ? $this->res('无法发起POST请求', 3) : curl_error($c);
        curl_close($c);
        return !$j ? mb_convert_encoding($r, 'UTF-8', ['ASCII', 'UTF-8', 'GB2312', 'GBK']) : json_decode(mb_convert_encoding($r, 'UTF-8', ['ASCII', 'UTF-8', 'GB2312', 'GBK']), true);
    }

    /**
     * 获取IP地址
     * @param string $i IP地址
     * @param bool $r 是否返回数组(非必填，默认true,当为false时，返回省份字符串)
     * @return array|string
     */
    public function getIpaddress($i, $r = true)
    {
        $s = preg_match('/\d+.\d+.\d+.\d+/', $i, $res);
        if (!$s) return false;
        $p = $res[0];
        $u = "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query={$p}&resource_id=6006";
        $a = $this->curl($u);
        if (!$r) return explode(' ', $a['data'][0]['location'])[0];
        return $a;
    }

    /**
     * 嵌入PHP文件
     * @param string $p 文件路径
     * @param bool $r 是否允许程序继续执行(非必填，默认false)
     * @return void
     */
    public function includePage($p, $r = false)
    {
        $this->q();
        if (!file_exists($p)) $this->res("缺少载入文件{$p}", 3);
        include($p);
        if (!$r) exit();
    }

    /**
     * 获取文件内容
     * @param string $f 文件路径
     * @param array $d 替换参数(非必填)
     * @return string
     */
    public function getHtmlCode($f, $d = [])
    {
        if (!file_exists($f)) $this->res("文件{$f}不存在", 3);
        $n = $v = [];
        foreach ($d as $k => $val) {
            $n[] = "{\${$k}}";
            $v[] = $val;
        }
        return str_replace($n, $v, file_get_contents(iconv('utf-8', 'gbk', $f)));
    }


    /**
     * 获取SQL语句
     * @param string $s 表名
     * @param array $a 参数
     * @param bool $w where条件(非必填，默认false)
     * @param array $d 去重字段(非必填，默认false)
     * @return array
     */
    public function getSql($s, $a, $w = false, $d = [])
    {
        $this->ajax($a);
        $f = $fv = $u = $sw = '';
        foreach ($a as $n) {
            $b = explode('=', $n);
            $i = explode(count($b) == 1 ? ':' : '=', $n)[0];
            $v = count($b) == 1 ? $_REQUEST[$i] : $b[1];
            $f .= $i != 'id' ? "`{$i}`," : '';
            $fv .= $i != 'id' ? "'{$v}'," : '';
            $u .= $i != 'id' ? "`{$i}`='{$v}'," : '';
            if ($d != false && in_array($i, $d)) {
                $sw .= $i != 'id' ? "`{$i}`='{$v}' AND" : '';
            }
        }
        $f = substr($f, 0, strlen($f) - 1);
        $fv = substr($fv, 0, strlen($fv) - 1);
        $u = substr($u, 0, strlen($u) - 1);
        $sw = substr($sw, 0, strlen($sw) - 4);
        $id = $this->is('id');
        $w = !$w ? "`id` = {$id}" : $w;
        $add = "INSERT INTO `{$s}` ({$f}) VALUES ({$fv});";
        $upd = "UPDATE `{$s}` SET {$u} WHERE {$w};";
        $que = "SELECT id FROM `{$s}` WHERE {$sw};";
        if (count($d) > 0 && $this->is('id') == '') {
            $res = $this->run($que);
            if ($res->num_rows > 0) $this->res('添加的数据已存在', 3);
        }
        $d = ['add' => $add, 'upd' => $upd, 'que' => $que];
        return $d;
    }

    /**
     * 执行SQL语句
     * @param string $s SQL语句
     * @param bool $r 是否返回结果(非必填，默认true)
     * @param string $c 成功提示(非必填，默认操作成功)
     * @param string $e 失败提示(非必填，默认操作失败)
     * @return bool|mysqli_result
     */
    public function run($s, $r = true, $c = '操作成功', $e = '操作失败')
    {
        $this->sqls[] = $s;
        $res = $this->conn->query($s);
        if (!$r) $this->res($res ? $c : $e, $res ? 1 : 3);
        return $res;
    }

    /**
     * layui查询数据1
     * @param string $s 表名
     * @param string $w where条件(非必填，默认空)
     * @param string $f 查询字段(非必填，默认*)
     * @param bool $l 是否分页(非必填，默认true)
     * @param bool $r 是否返回data(非必填，默认false)
     * @return 输出json|array
     */
    public function query($s, $w = '', $f = '*', $l = true, $r = false)
    {
        $data = [];
        $count = 0;
        $page = intval($this->is('page', 1));
        $limit = intval($this->is('limit', 100));
        $start = ($page - 1) * $limit;
        $li = $l ? "limit {$start},{$limit}" : '';
        $sql = "SELECT {$f} FROM `{$s}` {$w} {$li};";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) $data[] = $row;
        }
        $sql = "SELECT COUNT(*) AS `count` FROM `{$s}` {$w};";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $count = intval($row['count']);
        }
        $json = ['code' => 0, 'count' => $count, 'data' => $data, 'msg' => '调试成功', 'page' => $page, 'limit' => $limit, 'sqls' => $this->sqls];
        if ($r) return $json;
        $this->res($json);
    }


    /**
     * layui查询数据2
     * @param string $s 表名
     * @param array $a 查询参数(非必填，默认空)
     * @param string $f 查询字段(非必填，默认*)
     * @param string $o 排序(非必填，默认空)
     * @param bool $r 是否返回data(非必填，默认false)
     * @return 输出json|array
     */
    public function table($s, $a = [], $f = '*', $o = '', $r = false)
    {
        $w = '';
        foreach ($a as $k) {
            $b = explode(':', $k);
            $v = $this->is($b[0]);
            if ($v != '') {
                $l = isset($b[1]) ? $b[1] : 'like';
                if ($l == '=') $w .= $w ? " AND `{$b[0]}` {$l} '{$v}'" : " `{$b[0]}` {$l} '{$v}'";
                if ($l == 'like') $w .= $w ? " AND `{$b[0]}` {$l} '%{$v}%'" : " `{$b[0]}` {$l} '%{$v}%'";
            }
        }
        $w = $w ? "WHERE {$w}" : '';
        return $this->query($s, $w . $o, $f, true, $r);
    }

    /**
     * 批量删除数据
     * @param string $s 表名
     * @param bool|function $f 是否执行回调(非必填，默认false)
     * @param string $a 成功提示(非必填，默认删除成功)
     * @param string $b 失败提示(非必填，默认删除失败)
     * @return 输出json
     */
    public function rdel($s, $f = false, $a = '删除成功', $b = '删除失败')
    {
        $d = $this->is('item', []);
        $y = 0;
        foreach ($d as $k) {
            $id = $k['id'];
            if ($id == '1' && $s == 'user_data') $this->res('管理员账号禁止删除', 3);
            $q = "DELETE FROM `{$s}` WHERE `id` = {$id};";
            $this->run($q);
            $n = $this->conn->affected_rows;
            if ($n > 0) {
                $y += 1;
                $f && $f($k);
            }
        }

        $this->res($y > 0 ? $a : $b, $y > 0 ? 1 : 3);
    }

    /**
     * 获取系统配置信息
     * @param string $n 配置名称(非必填，默认system)
     * @return array
     */
    public function getSys($n = 'system')
    {
        $q = "SELECT `value` FROM `system_setup` WHERE `name` = '{$n}'";
        $r = $this->run($q);
        if ($r->num_rows == 0) return [];
        $d = $r->fetch_assoc();
        $j = json_decode($d['value'], true);
        return $j ? $j : json_decode('{}');
    }

    /**
     * 设置系统配置信息
     * @param string $n 配置名称(非必填，默认system)
     * @param array $d 配置信息(非必填，默认空)
     * @return void
     */
    public function setSys($n = 'system', $d = [])
    {
        $j = json_encode($d);
        $q = "UPDATE `system_setup` SET `value` = '{$j}' WHERE `name` = '{$n}'";
        $this->run($q);
    }

    /**
     * 上传文件(可通过?method=upload请求)
     * @return 输出json
     */
    public function _upload()
    {
        $c = count($_FILES);
        $c == 0 ? $this->res('请上传图片', 3) : true;
        $p = $this->is('path', 'upload/');
        $w = $this->is('wangEditor');
        $a = [];
        if (!is_dir($p)) mkdir($p);
        foreach ($_FILES as $k) {
            $f = $k['name'];
            $m = $k['tmp_name'];
            $e = $k['error'];
            $sz = $k['size'] / 1024 / 1024;
            $max = floatval($this->sys['upload_max']);
            if ($sz > $max) $this->res("文件只能上传{$max}M以内", 3);
            $ext = substr(strrchr($f, '.'), 1);
            if ($e != 0) exit($this->file_error($e));
            $this->verifiExt($ext);
            $nf = date('YmdHis') . rand(1000, 9999);
            $s = isset($_REQUEST['src']) ? str_replace('../', '', explode('?', $_REQUEST['src'])[0]) : false;
            if (strpos($s, '//') !== false) $s = false;
            if ($s) {
                move_uploaded_file($m, iconv('UTF-8', 'gb2312', $s));
                $d = ['url' => $_REQUEST['src'] . '?v=' . $nf];
                $this->res('上传成功', 1, $d);
            }

            switch ($this->sys['upload_type']) {
                case '1':
                    $n = md5($nf) . '.' . $ext;
                    break;
                case '2':
                    $n = base64_encode($nf) . '.' . $ext;
                    break;
                case '3':
                    $n = crypt($nf, 'str') . '.' . $ext;
                    break;
                default:
                    $n = $nf . '.' . $ext;
                    break;
            };

            $u = $p . $n;
            $o = $this->is('out', 'undefined');
            if ($o != 'undefined' && $this->sys['del_state'] == '1' && $o != '') $this->delFile($o);
            move_uploaded_file($m, iconv('UTF-8', 'gb2312', $u));
            $h = $this->host . str_replace('../', '', $p) . $n;
            $h = $this->uploadApi($u, $h);
            if ($c == 1) {
                if ($w != '') {
                    $a[] = ['url' => $h, 'href' => 'javascript:;', 'alt' => $f];
                    $j = ['errno' => 0, 'data' => $a, 'code' => '上传成功', 'icon' => 1];
                    $this->res($j);
                }
                $d = ['url' => $u, 'host' => $h];
                $this->res('上传成功', 1, $d);
            }
            $a[] = ['url' => $h, 'href' => 'javascript:;', 'alt' => $f];
        }
        $j  = ['errno' => 0, 'data' => $a, 'code' => '上传成功', 'icon' => 1];
        $this->res($j);
    }

    /**
     * 上传文件第三方接口
     * @param string $u 本地文件路径
     * @param string $h 上传接口
     * @return string 上传后的文件地址
     */
    public function uploadApi($u, $h)
    {
        if ($this->sys['upload_state'] == '0') return $h;
        $url = $this->sys['upload_name'];
        $k = "smfile\"; filename=\"{$u}\r\nContent-Type: text/plain\r\nAccept: \"";
        $data[$k] = file_get_contents($u);
        $data['file_id'] = '0';
        $res = json_decode($this->curl($url, $data), true);
        return isset($res['data']['host']) ? $res['data']['host'] : $this->res('上传错误', 3, $res);
    }

    /**
     * 验证文件后缀是否被禁止
     * @param string $ext 文件后缀
     * @return bool
     */
    public function verifiExt($ext)
    {
        $str = $this->sys['upload_ext'];
        $arr = explode(',', substr($str, 0, strlen($str) - 1));
        if (!in_array($ext, $arr)) $this->res("系统禁止上传.{$ext}后缀名的文件", 3);
        return true;
    }


    /**
     * 文件上传错误信息
     * @param int $t 错误代码
     * @return string
     */
    public function file_error($t)
    {
        $a = [
            [1, '上传的文件超过了 php.ini 中 upload_max_filesize选项限制的值'],
            [2, '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值'],
            [3, '文件只有部分被上传'],
            [4, '没有文件被上传'],
            [6, '找不到临时文件夹']
        ];
        foreach ($a as $k) if ($t == $k[0]) return $k[1];
        return 0;
    }

    /**
     * 删除文件
     * @param bool $f 文件路径
     * @return bool
     */
    public function delFile($f = false)
    {
        if (!$f) {
            $this->ajax(['url:url']);
            $u = $_REQUEST['url'];
            if (strstr($u, 'http')) {
                $file = (is_dir('php') ? 'upload/' : '../upload/') . explode('/upload/', $u)[1];
                if (file_exists($file)) {
                    unlink($file);
                    $this->res('文件删除成功', 1);
                }
                $this->res('文件不存在', 3);
            }
        }
        if ($f) {
            if (is_dir($f)) {
                $this->deldir($f);
                return true;
            }
            $file = (is_dir('php') ? 'upload/' : '../upload/') . explode('upload/', $f)[1];
            if (file_exists($file)) unlink($file);
            return true;
        }
        return true;
    }

    /**
     * 删除文件夹
     * @param string $dir 文件夹路径
     * @return bool
     */
    public function deldir($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }
        closedir($dh);
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 发送短信验证码(可通过?method=sendSmsCode请求)
     * @return 输出json
     */
    public function _sendSmsCode()
    {
        if ($this->sys['sms_state'] == '0') $this->res('未开启短信注册功能', 3);
        $this->ajax(['tel:tel', 'type:number']);
        $t = $_REQUEST['tel'];
        $y = intval($_REQUEST['type']);
        if (!preg_match("/^1[3456789]{1}\d{9}$/", $t)) $this->res('手机号码格式不正确', 3);
        if ($y == 0) {
            $sql = "SELECT `id` FROM  `user_data` WHERE `username` = '{$t}'";
            $res = $this->run($sql);
            if ($res->num_rows > 0) $this->res('帐号已被注册！', 5);
        }
        $sql = "SELECT `found_date` as d FROM  `smscode_list` WHERE `ip` = '{$this->ip}' ORDER BY `id` DESC limit 1";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $d = strtotime($row['d']);
            $s = strtotime(date('Y-m-d H:i:s'));
            $c = intval($this->sys['sms_second']);
            if ($s - $d < $c) {
                $i = $c - ($s - $d);
                $this->res("请在<b style='color:red;'>{$i}</b>秒后操作", 3);
            }
        }
        include 'sms.php';
        $sms = new _aliyunSms($this->sys['sms_accessKeyId'], $this->sys['sms_accessKeySecret']);
        $c = rand(100000, 999999);
        $m = $y == 0 ? $this->sys['sms_templateCode0'] : $this->sys['sms_templateCode1'];
        $res = $sms->_SendSms($this->sys['sms_signName'], $m, $t, $c);
        $this->db->add('smscode_list', [
            'type' => $y,
            'tel' => $t,
            'smscode' => $c,
            'ip' => $this->ip
        ]);
        $this->res('短信验证码已发送', 1);
    }

    /**
     * 验证短信验证码
     * @param string $t 手机号码
     * @param string $c 验证码
     * @return bool
     */
    public function isSmsCode($t, $c)
    {
        $w = "WHERE `tel` = '{$t}' ORDER BY `id` DESC limit 1";
        $sql = "SELECT `state`,`smscode` FROM  `smscode_list` {$w}";
        $res = $this->run($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $s = intval($row['state']);
            if ($s == 1) $this->res('短信验证码不正确！', 3);
            if ($c != $row['smscode']) $this->res('短信验证码不正确！', 3);
            $d = date('Y-m-d H:i:s');
            $sql = "UPDATE `smscode_list` SET `state` = '1',`veri_date` = '{$d}' {$w}";
            $res = $this->run($sql);
            return true;
        }
        $this->res('短信验证码不正确！', 3, '无发送记录');
    }

    /**
     * 发送消息到用户
     * @param string $u 对方用户名
     * @param string $t 消息分类
     * @param string $img 图标
     * @param string $title 标题
     * @param string $content 内容
     * @param string $b 分类消息下的子分类
     * @return bool
     */
    public function sendMessage($u, $t = 0, $img = '', $title = '', $content = '', $b = 0, $c = false)
    {
        if (!$c) $c = md5(date('YmdHis') . rand(10000, 99999));
        if (!is_numeric($u)) {
            $q = "SELECT `id` FROM  `user_data` WHERE `username` = '{$u}' limit 1;";
            $r = $this->run($q);
            if ($r->num_rows > 0) {
                $d = $r->fetch_assoc();
                $u = $d['id'];
            }
        }
        if ($this->sys['mailbox_state'] == '1') $this->sendMail(false, $title, $content);
        $this->db->add('user_message', [
            'user_id' => $this->id,
            'user_to' => $u,
            'type' => $t,
            'img' => $img,
            'title' => $title,
            'content' => $content,
            'code' => $c,
            'body_type' => $b
        ]);
    }

    /**
     * 发送邮件
     * @param string $mailbox 收件人(默认为系统邮箱)
     * @param string $title 标题
     * @param string $content 内容
     * @return bool
     */
    public function sendMail(string $mailbox = '', string $title = '', string $content = ''): bool
    {
        include 'smtp.php';
        $i = $this;
        $s = new Smtp($i->sys['mail_server'], $i->sys['mail_port'], true, $i->sys['mail_user'], $i->sys['mail_secret']);
        $s->debug = false;
        $mailbox = !$mailbox ? $i->sys['mail_mailbox'] : $mailbox;
        return $s->sendmail($mailbox, $i->sys['mail_account'], $title, $content, 'HTML');
    }

    /**
     * 注册请求方法集
     * @param string $s 数据表
     * @param string $w 条件(如果请求参数method不存在则会自动添加条件)
     * @return array|void
     */
    public function method($s = '', $w = '')
    {
        $t = $this;
        $m = $t->is('method');
        $l = intval($t->is('limit', 0));
        if ($l > 100) $_REQUEST['limit'] = 100;
        if ($m != '') {
            if (!preg_match("/^[a-zA-Z]+$/", $m)) $t->res('请求方法不正确', 3);
            $m = '_' . $m;
            if (!method_exists($t, $m)) $t->res('请求' . $m . '方法不存在', 3);
            $t->$m();
            exit();
        }
        $id = $t->is('id');
        $u = is_string($s) ? $s : false;
        if ($id != '' && $u) {
            $q = "SELECT * FROM  `{$u}` WHERE `id` = '{$id}'{$w} limit 1;";
            $r = $t->run($q);
            if ($r->num_rows > 0) {
                $d = $r->fetch_assoc();
                $t->info = $d;
                return $d;
            }
        }
    }

    /**
     * 排序表格数据(可通过?method=SetSort请求)
     * @return 输出json
     */
    public function _SetSort()
    {
        $t = $this;
        $t->ajax(['data', 'surface', 'page', 'limit']);
        $d = $_REQUEST['data'];
        if (!is_array($d)) $t->res('参数异常', 3);
        $s = $_REQUEST['surface'];
        $p = intval($_REQUEST['page']);
        $l = intval($_REQUEST['limit']);
        foreach ($d as $k) {
            $id = $k['id'];
            $i = ($k['indexs'] + ($p - 1) * $l) + 1;
            $q = "UPDATE `{$s}` SET `indexs` = '{$i}' WHERE `id` = {$id};";
            $r = $t->run($q);
            if (!$r) $t->res('排序失败', 3);
        }
        $t->res('排序成功', 1);
    }

    /**
     * 验证表单数据
     * @param array $p 验证规则 例：['username'=>['username','required']]
     * @param array $b 数据来源(默认$_REQUEST)
     * @return array
     */
    public function form($p = [], $b = '')
    {
        $t = $this;
        if (!$b) $b = $_REQUEST;
        if (!is_array($p)) $t->res("格式必须是数组", 5);
        $a = [
            'id' => '/^\d+$/',
            'username' => '/^\w{5,20}$/',
            'password' => '/^[a-zA-Z0-9]{6,16}$/',
            'token' => '/^[a-f0-9]{32}$/',
            'number' => '/^[0-9]*$/',
            'url' => '/^(https|http|ftp|rtsp|mms)?:\/\/[^\s]+/',
            'phone' => '/^1[0-9]{10}$/',
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'idcard' => '/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/',
            'ip' => '/^((25[0-5]|2[0-4]\d|[01]?\d\d?)($|(?!\.$)\.)){4}$/',
            'mac' => '/^([A-Fa-f0-9]{2}:){5}[A-Fa-f0-9]{2}$/',
            'zh' => '/^[\u4e00-\u9fa5]{0,}$/',
            'date' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',
            'datetime' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',
            'time' => '/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/',
            'qq' => '/^[1-9][0-9]{4,9}$/',
            'money' => '/^[0-9]+(.[0-9]{1,2})?$/',
            'captcha' => '/^[a-zA-Z0-9]{4}$/',
            'boolean' => '/^(true|false)$/',
            'letter' => '/^[a-zA-Z]+$/',
            'method' => '/^[a-zA-Z]+$/',
        ];
        foreach ($p as $n => $i) {
            $v = isset($b[$n]) ? $b[$n] : '';
            if (in_array('required', $i) && !$v) $t->res("{$n}不能为空", 5);
            foreach ($i as $k) if (isset($a[$k]) && $v) if (is_string($v) && !preg_match($a[$k], $v)) $t->res("{$n}格式不正确", 5);
        }
        return $b;
    }
}
