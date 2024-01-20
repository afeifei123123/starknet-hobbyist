<?php
include '../php/api.php';
class _web extends _api
{
    //获取系统设置
    public function _data()
    {
        $this->res('获取配置成功', 1, $this->sys);
    }

    //保存系统设置
    public function _set()
    {
        $value = json_encode($_POST, JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE `system_setup` SET `value` = '{$value}' WHERE `name` = 'system';";
        $this->run($sql, false);
    }

    //获取角色列表
    public function _roles()
    {
        $sql = "select `id`,`name` from `roles_list` ORDER BY `indexs`,`id` ASC";
        $res = $this->run($sql);
        $d = [];
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $d[] = $row;
            }
        }
        $this->res('调试成功', 1, $d);
    }
}
$web = new _web(2, "id", false, true);
$web->method();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>系统设置</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <style>
        .upload-ext {
            border: 1px solid #ebebeb;
            background-color: #FFFFFF;
            padding: 6px 15px;
        }

        .upload-ext-item {
            display: inline-block;
            vertical-align: middle;
            padding: 2px 10px;
            position: relative;
            background-color: var(--color);
            border-radius: 3px;
            color: #FFFFFF;
            font-size: 12px;
            line-height: 15px;
            margin-right: 5px;
            padding-right: 5px;
        }

        .upload-ext-item>.layui-icon.layui-icon-close {
            font-size: 12px;
            float: right;
            margin-left: 5px;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
        }

        input[name=add-ext] {
            border: none;
            width: 80px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body layui-form">
                    <div class="layui-tab layui-tab-brief">
                        <ul class="layui-tab-title">
                            <li class="layui-this">网站设置</li>
                            <li>接口设置</li>
                            <li>附件设置</li>
                            <li>高级设置</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <div class="layui-collapse">
                                    <div class="layui-colla-item">
                                        <h2 class="layui-colla-title">站点信息</h2>
                                        <div class="layui-colla-content layui-show">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>网站状态</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="server_state" value="1" title="开启" />
                                                    <input type="radio" name="server_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span class="layui-font-red">* 是否启用网站服务，关闭后普通用户将禁止使用！</span>
                                                </div>
                                            </div>
                                            <div class="server_state-box">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span class="layui-must">*</span>
                                                        <span>网站名称</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="title" class="layui-input" lay-count="40" placeholder="请输入网站名称" lay-verify="required" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>网站名称，将显示在浏览器窗口标题等位置</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item layui-col-md6">
                                                    <label class="layui-form-label">
                                                        <span>网站描述</span>
                                                    </label>
                                                    <div class="layui-input-block">
                                                        <textarea name="Keywords" class="layui-textarea" lay-count="100" placeholder="请输入网站描述信息"></textarea>
                                                        <div class="layui-form-mid layui-word-aux">
                                                            <span>网站描述，有利于查询引擎抓取相关信息，建议不超过80个字符</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>ICP备案号</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="icp" class="layui-input" placeholder="请输入域名ICP备案号" lay-count="20" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>还没备案？</span>
                                                        <a href="https://console.cloud.tencent.com/beian/manage/verification" target="_blank" class="layui-table-link">腾讯云备案</a>
                                                        <span class="layui-table-divide"></span>
                                                        <a href="https://beian.aliyun.com/" target="_blank" class="layui-table-link">阿里云备案</a>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span class="layui-must">*</span>
                                                        <span>系统时区</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <select name="timezone" lay-verify="required">
                                                            <option value="Asia/Shanghai">中国/上海</option>
                                                            <option value="Asia/Tokyo">日本/东京</option>
                                                            <option value="America/New_York">美国/纽约</option>
                                                            <option value="Europe/Berlin">德国/柏林</option>
                                                            <option value="Europe/Paris">法国/巴黎</option>
                                                            <option value="Europe/London">英国/伦敦</option>
                                                            <option value="Australia/Sydney">澳大利亚/悉尼</option>
                                                            <option value="Asia/Seoul">韩国/首尔</option>
                                                            <option value="Asia/Hong_Kong">中国香港</option>
                                                            <option value="Asia/Taipei">中国台湾</option>
                                                            <option value="Asia/Macau">中国澳门</option>
                                                            <option value="Asia/Kuala_Lumpur">马来西亚/吉隆坡</option>
                                                            <option value="Asia/Singapore">新加坡</option>
                                                            <option value="Asia/Bangkok">泰国/曼谷</option>
                                                            <option value="Asia/Manila">菲律宾/马尼拉</option>
                                                            <option value="Asia/Baku">阿塞拜疆/巴库</option>
                                                            <option value="Asia/Dubai">阿联酋/迪拜</option>
                                                            <option value="Asia/Kabul">阿富汗/喀布尔</option>
                                                            <option value="Asia/Karachi">巴基斯坦/卡拉奇</option>
                                                            <option value="Asia/Tehran">伊朗/德黑兰</option>
                                                            <option value="Asia/Yerevan">亚美尼亚/埃里温</option>
                                                            <option value="Asia/Yekaterinburg">俄罗斯/叶卡捷琳堡</option>
                                                            <option value="Asia/Kolkata">印度/加尔各答</option>
                                                            <option value="Asia/Kathmandu">尼泊尔/加德满都</option>
                                                            <option value="Asia/Colombo">斯里兰卡/科伦坡</option>
                                                            <option value="Asia/Dhaka">孟加拉国/达卡</option>
                                                            <option value="Asia/Almaty">哈萨克斯坦/阿拉木图</option>
                                                            <option value="Asia/Novosibirsk">俄罗斯/新西伯利亚</option>
                                                            <option value="Asia/Rangoon">缅甸/仰光</option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>设置系统所在时区，中国大陆请选择“中国/上海”</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>引用版本号</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="text" name="version" class="layui-input" placeholder="请输入网站版本号" lay-count="10" />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>修改版本号可以清理系统缓存，不填写版本号将默认不缓存，注意：不填写版本号将导致网站访问速度变慢</span>
                                                </div>
                                            </div>
                                            <div class="server_state-box">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span class="layui-must">*</span>
                                                        <span>网站ICO</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <div class="upload-item" style="width: 25px;height: 25px;" ext=".ico">
                                                            <img src="<?php echo file_exists('../favicon.ico') ? '../favicon.ico' : '../../favicon.ico'; ?>?v=<?php echo $web->v; ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>建议尺寸：25*25px，仅支持ico格式</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span class="layui-must">*</span>
                                                        <span>网站LOGO</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <div class="upload-item" style="width: 35px;height: 35px;">
                                                            <img src="../images/logo.png?v=<?php echo $web->v; ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>建议尺寸：35*35px，支持jpg，jpeg，png格式</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span class="layui-must">*</span>
                                                        <span>登录页广告图</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="logon_img" class="layui-input layui-hide">
                                                        <div class="upload-item" style="width: 80px;height: 100px;" path="upload/system/" del="true">
                                                            <span>上传图片</span>
                                                        </div>
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>建议尺寸：350*540px，支持jpg，jpeg，png格式</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-colla-item">
                                        <h2 class="layui-colla-title">公告设置</h2>
                                        <div class="layui-colla-content layui-show">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>公告状态</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="notice_state" value="1" title="显示" checked />
                                                    <input type="radio" name="notice_state" value="0" title="隐藏" />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span class="layui-font-red">* 是否显示网站公告，关闭后将不显示公告信息！</span>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>公告内容</span>
                                                </label>
                                                <div class="layui-input-block">
                                                    <textarea name="notice_text" class="layui-textarea" placeholder="请输入公告内容"></textarea>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>公告内容，支持html代码</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-collapse">
                                    <div class="layui-colla-item">
                                        <h2 class="layui-colla-title">
                                            <i class="layui-icon layui-icon-email" style="color: #007AFF;"></i>
                                            <span>SMTP邮件</span>
                                        </h2>
                                        <div class="layui-colla-content">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>状态</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="mail_state" value="1" title="开启" />
                                                    <input type="radio" name="mail_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>是否接收来自网站的服务通知</span>
                                                </div>
                                            </div>
                                            <div class="mail_state-box">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>主机地址</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="mail_server" class="layui-input" placeholder="请输入主机地址" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>发送邮箱的smtp地址，例如：smtp.qq.com</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>端口号</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="mail_port" class="layui-input" placeholder="请输入端口号 " />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span class="layui-font-red">*</span>
                                                        <span class="layui-font-red">注意：请阅读STMP服务官方文档获取端口，一般发送失败都是端口问题！</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>邮箱账号</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="mail_account" class="layui-input" lay-type="email" placeholder="请输入邮箱账号" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>用于登录发送给客户的邮箱账号</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>用户名</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="mail_user" class="layui-input" placeholder="请输入用户名" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>发送邮件显示的用户名称</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>授权码</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="mail_secret" class="layui-input" placeholder="请输入授权码" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>STMP授权码</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>同步推送</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="radio" name="mailbox_state" value="1" title="开启" />
                                                        <input type="radio" name="mailbox_state" value="0" title="关闭" checked />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>开启后当网站有通知消息时会给网站管理员邮箱也发送一封通知邮件</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>管理员邮箱</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="mail_mailbox" class="layui-input" lay-type="email" placeholder="请输入管理员邮箱号" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>用于接收来自网站服务信息的邮箱账号，支持多个，以“,”符号分割，例如：123456@qq.com,654321@qq.com</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-colla-item">
                                        <h2 class="layui-colla-title">
                                            <i class="layui-icon layui-icon-cellphone" style="color: #ff5722;"></i>
                                            <span>SMS短信</span>
                                        </h2>
                                        <div class="layui-colla-content">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>状态</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="sms_state" value="1" title="开启" />
                                                    <input type="radio" name="sms_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>开启后平台将提供短信服务，例如：注册及找回密码服务等！</span>
                                                </div>
                                            </div>
                                            <div class="sms_state-box">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>短信间隔</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="sms_second" class="layui-input" placeholder="请输入短信发送间隔时间" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>用于限制用户发送短信频率（秒）</span>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>KeyId</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="sms_accessKeyId" class="layui-input" placeholder="请输入阿里云KeyId" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>阿里云短信密钥AccessKey ID</span>
                                                        <span class="layui-table-divide"></span>
                                                        <a href="https://usercenter.console.aliyun.com/#/manage/ak" target="_blank" class="layui-table-link">进入平台</a>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>KeySecret</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="sms_accessKeySecret" class="layui-input" placeholder="请输入阿里云KeySecret" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>阿里云短信密钥AccessKey Secret。</span>
                                                        <span class="layui-table-divide"></span>
                                                        <a href="https://usercenter.console.aliyun.com/#/manage/ak" target="_blank" class="layui-table-link">进入平台</a>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>签名模板</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="sms_signName" class="layui-input" placeholder="请输入阿里云签名模板" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>SMS签名模板名称</span>
                                                        <span class="layui-table-divide"></span>
                                                        <a href="https://dysms.console.aliyun.com/dysms.htm?spm=5176.8195934.J_5834642020.4.4b924378pjVZ0n#/domestic/text/sign" target="_blank" class="layui-table-link">进入平台</a>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>注册CODE</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="sms_templateCode0" class="layui-input" placeholder="请输入阿里云注册账号CODE" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>模板CODE</span>
                                                        <span class="layui-table-divide"></span>
                                                        <a href="https://dysms.console.aliyun.com/dysms.htm?spm=5176.8195934.J_5834642020.4.4b924378pjVZ0n#/domestic/text/template" target="_blank" class="layui-table-link">进入平台</a>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>找回CODE</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="sms_templateCode1" class="layui-input" placeholder="请输入阿里云找回密码CODE" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>模板CODE</span>
                                                        <span class="layui-table-divide"></span>
                                                        <a href="https://dysms.console.aliyun.com/dysms.htm?spm=5176.8195934.J_5834642020.4.4b924378pjVZ0n#/domestic/text/template" target="_blank" class="layui-table-link">进入平台</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">保存风格</label>
                                    <div class="layui-input-inline">
                                        <select name="upload_type" lay-verify="required">
                                            <option value="0">Ymd</option>
                                            <option value="1">md5</option>
                                            <option value="2">base64</option>
                                            <option value="3">crypt</option>
                                        </select>
                                    </div>
                                    <label class="layui-form-label">最大限制</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="upload_max" class="layui-input" placeholder="请输入允许上传的文件大小" />
                                    </div>
                                    <div class="layui-form-mid layui-word-aux">
                                        <span>上传文件不能最大值限制（M）</span>
                                    </div>
                                </div>
                                <div class="layui-form-item layui-col-md6">
                                    <label class="layui-form-label">
                                        <span>允许上传</span>
                                    </label>
                                    <div class="layui-input-block">
                                        <div class="upload-ext"></div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">
                                        <span>自动删除文件</span>
                                    </label>
                                    <div class="layui-input-inline">
                                        <input type="radio" name="del_state" value="1" title="开启" />
                                        <input type="radio" name="del_state" value="0" title="关闭" checked />
                                    </div>
                                    <div class="layui-form-mid layui-word-aux">
                                        <span>上传新文件后，把原先的文件删除</span>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">
                                        <span>转存文件</span>
                                    </label>
                                    <div class="layui-input-inline">
                                        <input type="radio" name="upload_state" value="1" title="开启" />
                                        <input type="radio" name="upload_state" value="0" title="关闭" checked />
                                    </div>
                                    <div class="layui-form-mid layui-word-aux">
                                        <span>开启后系统将您的文件上传至第三方服务器且本地服务不进行保存！</span>
                                    </div>
                                </div>
                                <div class="upload_state-box">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">
                                            <span>上传地址</span>
                                        </label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="upload_name" class="layui-input" placeholder="请输入第三方服务器地址" />
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">
                                            <span>选择将文件转上传的平台，默认文件名称file，返回数据格式{'data':{'host':'图片地址'}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-collapse">
                                    <div class="layui-colla-item">
                                        <h2 class="layui-colla-title">
                                            <i class="layui-icon layui-icon-username" style="color: #aa55ff;"></i>
                                            <span>登录注册</span>
                                        </h2>
                                        <div class="layui-colla-content">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>用户注册</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="register_state" value="1" title="开启" />
                                                    <input type="radio" name="register_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>开启或者关闭网站用户注册！</span>
                                                </div>
                                            </div>
                                            <div class="register_state-box">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">默认角色</label>
                                                    <div class="layui-input-inline">
                                                        <select name="default_roles">
                                                            <option value=""></option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>用户注册成功后默认的角色及权限</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>找回密码</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="retpawd_state" value="1" title="开启" />
                                                    <input type="radio" name="retpawd_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span class="layui-font-red">*</span>
                                                    <span class="layui-font-red">开启或者关闭网站用户找回密码功能，注意：如果短信功能不启用也是无法使用找回密码功能！</span>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>验证码</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="captcha_state" value="1" title="开启" />
                                                    <input type="radio" name="captcha_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>开启或者关闭登录页面的图片验证码！</span>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>记住密码</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="recall_state" value="1" title="开启" />
                                                    <input type="radio" name="recall_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>开启或者关闭登录页面的记住密码选项</span>
                                                </div>
                                            </div>
                                            <div class="recall_state-box">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>自动登录</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="radio" name="autologon_state" value="1" title="开启" lay-filter="autologon_state" />
                                                        <input type="radio" name="autologon_state" value="0" title="关闭" lay-filter="autologon_state" checked />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>开启自动登录需要关闭验证码，打开记住密码</span>
                                                    </div>
                                                </div>
                                                <div class="autologon_state-box">
                                                    <div class="layui-form-item">
                                                        <label class="layui-form-label">
                                                            <span>登录延迟</span>
                                                        </label>
                                                        <div class="layui-input-inline">
                                                            <input type="text" name="autologon_time" class="layui-input" placeholder="请输入延迟自动登录时间" />
                                                        </div>
                                                        <div class="layui-form-mid layui-word-aux">
                                                            <span>延迟可以让用户选择自己是否需要自动登录账号</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>登录推送</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="lanq_state" value="1" title="开启" />
                                                    <input type="radio" name="lanq_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>状态用户登录或者退出行为告知管理员</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-colla-item">
                                        <h2 class="layui-colla-title">
                                            <i class="layui-icon layui-icon-vercode" style="color: #5fb878;"></i>
                                            <span>安全防护</span>
                                        </h2>
                                        <div class="layui-colla-content">
                                            <div class="layui-form-item">
                                                <div class="layui-col-md6">
                                                    <label class="layui-form-label">
                                                        <span>系统违禁词</span>
                                                    </label>
                                                    <div class="layui-input-block">
                                                        <input type="text" name="ban_word" class="layui-input" placeholder="请输入系统拦截的关键词" />
                                                    </div>
                                                </div>
                                                <div class="layui-col-md6">
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>正则表达式，提交表单检测到违禁词禁止提交</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>转换非本站链接</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="unless_state" value="1" title="开启" />
                                                    <input type="radio" name="unless_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>开启后跳转网站会前告知用户是否进行跳转</span>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>插件服务</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="plug_state" value="1" title="开启" />
                                                    <input type="radio" name="plug_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>关闭后【软件市场】安装的插件将不起作用</span>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>访问权限</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="juris_state" value="1" title="开启" />
                                                    <input type="radio" name="juris_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>关闭后将不在验证普通用户的访问权限</span>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>记录请求</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="request_state" value="1" title="开启" />
                                                    <input type="radio" name="request_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>系统将记录每次前端发起的GET/POST请求</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-colla-item">
                                        <h2 class="layui-colla-title">
                                            <i class="layui-icon layui-icon-dialogue" style="color: #ff5722;"></i>
                                            <span>聊天配置</span>
                                        </h2>
                                        <div class="layui-colla-content">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <span>智能机器人</span>
                                                </label>
                                                <div class="layui-input-inline">
                                                    <input type="radio" name="robot_state" value="1" title="开启" />
                                                    <input type="radio" name="robot_state" value="0" title="关闭" checked />
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">
                                                    <span>开启后将取代管理员账号自动答复</span>
                                                </div>
                                            </div>
                                            <div class="robot_state-box">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">
                                                        <span>接收频率</span>
                                                    </label>
                                                    <div class="layui-input-inline">
                                                        <input type="text" name="msg_time" class="layui-input" placeholder="请输入聊天刷新频率" />
                                                    </div>
                                                    <div class="layui-form-mid layui-word-aux">
                                                        <span>每多少毫秒刷新一次数据</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-footer">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">保存设置</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/dist/layui/layui.js?v=<?php echo $web->v; ?>"></script>
<script src="../js/api.js?v=<?php echo $web->v; ?>"></script>
<script>
    //删除后缀
    $(document).on("click", ".upload-ext-item>i", function() {
        $(this).parent().fadeOut(300, function() {
            $(this).remove();
        });
    });

    //添加文件后缀
    $(document).on("keydown", "[name=add-ext]", function(e) {
        var keyCode = e.keyCode || e.which || e.charCode;
        var ctrlKey = e.ctrlKey || e.metaKey;
        if (keyCode == 13) {
            var value = $(this).val();
            if (value != "") {
                var item = '<div class="upload-ext-item"><span>' + value +
                    '</span><i class="layui-icon layui-icon-close"></i></div>';
                $(this).before(item);
                $(this).val("");
            }
        }
    });

    //打开链接的窗口
    $(".openPage").click(function(event) {
        event.preventDefault();
        var title = $(this).attr("data-title");
        var url = $(this).attr("href");
        window.parent.frames.newWindow(url, title, location.href);
        return false;
    });

    //保存设置
    form.on("submit(submit)", function(data) {
        data.field.upload_ext = get_ext();
        $.ajax({
            url: api.url('set'),
            type: 'POST',
            dataType: 'json',
            data: data.field,
            beforeSend: function() {
                layer.msg("正在保存", {
                    icon: 16,
                    shade: 0.2,
                    time: false
                });
            },
            success: function(data) {
                layer.msg(data.msg, {
                    icon: data.code,
                    anim: 1
                });
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
        return false;
    });

    //初始化
    function init() {

        $.ajax({
            url: api.url('data'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.code == 1) {
                    for (var key in data.data) {
                        //开关类型
                        var value = data.data[key];
                        if (key.indexOf("_state") != -1) {
                            $("[name=" + key + "]").prop("checked", false);
                            $("[name=" + key + "][value='" + value + "']").prop("checked", true);
                            $(`.${key}-box`).addClass("layui-hide");
                            if (value == '1') {
                                $(`.${key}-box`).removeClass("layui-hide");
                            }
                        } else if (key.indexOf("_img") != -1) {
                            var el = $("[name=" + key + "]");
                            el.val(value);
                            if (value != "") {
                                var img = `<img src="${value}" />`,
                                    e = el.parents(".layui-form-item").find(".upload-item");
                                e.html(img);
                                if (e.attr('del')) {
                                    e.append('<div class="upload-del"><span class="del-file">删除</span></div>');
                                }
                            }
                        } else {
                            $("[name=" + key + "]").val(value);
                        }
                    }
                    //特殊处理
                    var arr = data.data.upload_ext.split(",");
                    var exldom = $(".upload-ext");
                    exldom.html("");
                    for (var key in arr) {
                        var ext = arr[key];
                        if (ext != "") {
                            var item = '<div class="upload-ext-item"><span>' + ext +
                                '</span><i class="layui-icon layui-icon-close"></i></div>';
                            exldom.append(item);
                        }
                    }
                    exldom.append('<input type="text" name="add-ext" placeholder="文件后缀名"/>');
                    form.render();
                    api.f(api);
                    api.count();
                    roles(data.data.default_roles);

                    //监听checked选中事件
                    form.on('radio', function(data) {
                        var name = data.elem.name;
                        if (name.indexOf("_state") != -1) {
                            $(`.${name}-box`).addClass("layui-hide");
                            if (data.value == '1') {
                                $(`.${name}-box`).removeClass("layui-hide");
                            }
                        }
                    });
                } else {
                    layer.msg(data.msg, {
                        icon: data.code
                    });
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });

    }

    function get_ext() {
        var html = "";
        $(".upload-ext-item").each(function() {
            var ext = $(this).children("span").text();
            html += ext + ",";
        });
        return html;
    }
    init();

    function roles(value) {
        $.ajax({
            url: api.url('roles'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.code == 1) {
                    var elem = $("[name=default_roles]");
                    for (var key in data.data) {
                        var json = data.data[key];
                        var item = '<option value="' + json.id + '">' + json.name + '</option>';
                        elem.append(item);
                    }
                    elem.val(value);
                    form.render();
                }
            },
            error: r => layer.alert(r.responseText, {
                icon: 2
            })
        });
    }
</script>

</html>