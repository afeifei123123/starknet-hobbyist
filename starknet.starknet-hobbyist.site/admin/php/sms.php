<?php

/**
 * 阿里云短信接口
 */

class _aliyunSms
{
    public $AccessKeyId; //AccessKeyId
    public $AccessSecret; //AccessSecret
    public $url; //请求地址
    public $data; //请求参数

    /**
     * 构造函数
     * @param $AccessKeyId 阿里云短信接口AccessKeyId
     * @param $AccessSecret 阿里云短信接口AccessSecret
     */
    public function __construct($AccessKeyId, $AccessSecret)
    {
        $this->AccessKeyId = $AccessKeyId;
        $this->AccessSecret = $AccessSecret;
        $this->url = "https://dysmsapi.aliyuncs.com";
        $this->data = $this->_data();
    }

    /**
     * 合并通用参数
     * @return array
     */
    public function _data()
    {
        date_default_timezone_set("GMT"); //设置标准时区
        $data = [
            "AccessKeyId" => $this->AccessKeyId, //AccessKeyId
            "Format" => "json", //返回类型
            "RegionId" => "cn-hangzhou", //服务器地址
            "SignatureMethod" => "HMAC-SHA1", //签名方式
            "SignatureNonce" => rand(1000, 9999), //随机字符
            "SignatureVersion" => "1.0", //签名算法版本
            "Timestamp" => date("Y-m-d\TH:i:s\Z"), //请求的时间戳
            "Version" => "2017-05-25", //API版本
        ];
        return $data;
    }

    /**
     * 使用urlencode编码后，将"+","*","%7E"做替换即满足ECS API规定的编码规范
     * @param  string $str 待编码字符串
     * @return string
     */
    public function _percentEncode($str)
    {
        $str = urlencode($str);
        return str_replace(["+", "*", "%7E"], ["%20", "%2A", "~"], $str);
    }

    /**
     * 发起HTTP请求
     * @param $url 请求地址
     * @return string
     */
    public function _http($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            $this->res("无法发起HTTP请求", 3, curl_error($curl));
        }
        curl_close($curl);
        return $data;
    }

    /**
     * 输出JSON数据
     * @param $c msg字段信息
     * @param $i code字段信息
     * @param string $d data字段信息
     */
    public function res($c, $i, $d = "")
    {
        header("Content-type:text/html;charset=utf-8");
        $j = ["msg" => $c, "code" => intval($i), "data" => $d];
        exit(json_encode($j, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 生成签名
     * @return string
     */
    public function _getSign()
    {
        // 将参数Key按字典顺序排序
        ksort($this->data);
        // 生成规范化请求字符串
        $str = '';
        foreach ($this->data as $key => $value) {
            $a = $this->_percentEncode($key);
            $b = $this->_percentEncode($value);
            $str .= '&' . $a . '=' . $b;
        }
        $stringToSign = 'GET&%2F&' . $this->_percentEncode(substr($str, 1));
        $sha1 = hash_hmac('sha1', $stringToSign, $this->AccessSecret . '&', true);
        $base64 = base64_encode($sha1);
        return $base64;
    }

    /**
     * 发起请求
     * @return mixed
     */
    public function _request()
    {
        $this->data["Signature"] = $this->_getSign();
        $url = $this->url . "?" . http_build_query($this->data);
        $res = $this->_http($url);
        $json = json_decode($res, true);
        if ($json["Code"] != "OK") {
            $this->res($json["Message"], "3", $json);
        }
        return $json;
    }

    /**
     * 发送短信
     * @param $SignName 短信签名
     * @param $TemplateCode 短信模板ID
     * @param $tel 手机号
     * @param $code 验证码
     * @param string $OutId 外部流水扩展字段
     * @return mixed
     */
    public function _SendSms($SignName, $TemplateCode, $tel, $code, $OutId = "123456")
    {
        $this->data["Action"] = "SendSms";
        $this->data["PhoneNumbers"] = $tel;
        $this->data["SignName"] = $SignName;
        $this->data["TemplateCode"] = $TemplateCode;
        $this->data["TemplateParam"] = "{\"code\":\"{$code}\"}";
        $this->data["TemplateParam"] = "{\"code\":\"{$code}\"}";
        $this->data["OutId"] = $OutId;
        return $this->_request();
    }

    /**
     * 查询短信发送详情
     * @param $tel 手机号
     * @return mixed
     */
    public function _QuerySendDetails($tel)
    {
        $this->data["Action"] = "QuerySendDetails";
        $this->data["PhoneNumber"] = $tel;
        $this->data["SendDate"] = date("Ymd");
        $this->data["CurrentPage"] = 1;
        $this->data["PageSize"] = 100;
        return $this->_request();
    }
}
