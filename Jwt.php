<?php

namespace app\wcore;


use Exception;

/**
 * PHP实现jwt
 */
class Jwt extends WObject {

    //头部
    private  $header=array(
        'alg'=>'HS256', //生成signature的算法
        'typ'=>'JWT'    //类型
    );
    //异常类
    private $exception;
    public $exceptionCode = 403;
    //使用HMAC生成信息摘要时所使用的密钥
    private  $key;
    public $base64decodeheader;
    public $payload;
    function __construct($key="",$exception=""){
        $this->key = $key;
        $this->exception = $exception?: Exception::class;
    }
    /**
     * 获取jwt token
     * @param array $payload jwt载荷   格式如下非必须
     * [
     *  'iss'=>'jwt_admin',  //该JWT的签发者
     *  'iat'=>time(),  //签发时间
     *  'exp'=>time()+7200,  //过期时间
     *  'nbf'=>time()+60,  //该时间之前不接收处理该Token
     *  'sub'=>'www.admin.com',  //面向的用户
     *  'jti'=>md5(uniqid('JWT').time())  //该Token唯一标识
     * ]
     * @return bool|string
     */
    public  function GetToken(array $payload)
    {
        if(is_array($payload))
        {
            $base64header=self::Base64UrlEncode(json_encode($this->header,JSON_UNESCAPED_UNICODE));
            $base64payload=self::Base64UrlEncode(json_encode($payload,JSON_UNESCAPED_UNICODE));
            $token=$base64header.'.'.$base64payload.'.'.self::Signature($base64header.'.'.$base64payload,$this->key,$this->header['alg']);
            return $token;
        }else{
            return false;
        }
    }

    public function GetPayLoad(string $token){
        $tokens = explode('.', $token);
        if (count($tokens) != 3)
            throw new $this->exception("jwt校验失败：格式错误",400);

        list($base64header, $base64payload, $sign) = $tokens;


        $payload = json_decode(self::Base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);
        return $payload;
    }
    /**
     * 验证token是否有效,默认验证exp,nbf,iat时间
     * @param string $Token 需要验证的token
     * @return bool|string
     */
    public  function VerifyToken( $token="",$isBase64Key=false,$log="")
    {
        if(!$token){
            throw new $this->exception("token不能为空",$this->exceptionCode);
        }
        $tokens = explode('.', $token);
        if (count($tokens) != 3)
            throw new $this->exception("jwt校验失败：格式错误",$this->exceptionCode);

        list($base64header, $base64payload, $sign) = $tokens;

        //获取jwt算法
        $base64decodeheader = json_decode(self::Base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
        $payload = json_decode(self::Base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);


        if (empty($base64decodeheader['alg'])){
            throw new $this->exception("jwt校验失败：header缺少alg",$this->exceptionCode);
        }

        $key = $isBase64Key?self::Base64UrlDecode($this->key):$this->key;
        //签名验证
        if (self::Signature($base64header . '.' . $base64payload,$key, $base64decodeheader['alg']) !== $sign){
            throw new $this->exception("jwt校验失败：签名算法失败",$this->exceptionCode);
        }



        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time()){
            throw new $this->exception("jwt校验失败：签发时间大于当前服务器时间验证失败",$this->exceptionCode);
        }


        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time()){
            throw new $this->exception("jwt校验失败：过期时间小于当前服务器时间验证失败",$this->exceptionCode);
        }


        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time()){
            throw new $this->exception("jwt校验失败：该nbf时间之前不接收处理该Token",417);
        }
        return $payload;
    }




    /**
     * base64UrlEncode   https://jwt.io/  中base64UrlEncode编码实现
     * @param string $input 需要编码的字符串
     * @return string
     */
    private  function Base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * base64UrlEncode  https://jwt.io/  中base64UrlEncode解码实现
     * @param string $input 需要解码的字符串
     * @return bool|string
     */
    private  function Base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * HMACSHA256签名   https://jwt.io/  中HMACSHA256签名实现
     * @param string $input 为base64UrlEncode(header).".".base64UrlEncode(payload)
     * @param string $key
     * @param string $alg   算法方式
     * @return mixed
     */
    private  function Signature(string $input, string $key, string $alg = 'HS256')
    {
        $alg_config=array(
            'HS256'=>'sha256'
        );
        return self::Base64UrlEncode(hash_hmac($alg_config[$alg], $input, $key,true));
    }
    public function SetKey($key){
        $this->key = $key;
        return $this;
    }
}

//    //测试和官网是否匹配begin
//    $payload=array('sub'=>'1234567890','name'=>'John Doe','iat'=>1516239022);
//    $jwt=new Jwt;
//    $token=$jwt->getToken($payload);
//    echo "<pre>";
//    echo $token;
//
//    //对token进行验证签名
//    $getPayload=$jwt->verifyToken($token);
//    echo "<br><br>";
//    var_dump($getPayload);
//    echo "<br><br>";
//    //测试和官网是否匹配end
//
//
//    //自己使用测试begin
//    $payload_test=array('iss'=>'admin','iat'=>time(),'exp'=>time()+7200,'nbf'=>time(),'sub'=>'www.admin.com','jti'=>md5(uniqid('JWT').time()));;
//    $token_test=Jwt::getToken($payload_test);
//    echo "<pre>";
//    echo $token_test;
//
//    //对token进行验证签名
//    $getPayload_test=Jwt::verifyToken($token_test);
//    echo "<br><br>";
//    var_dump($getPayload_test);
//    echo "<br><br>";
//自己使用时候end
