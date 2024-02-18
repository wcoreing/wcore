<?php
/**
 * Created by PhpStorm.
 * User: weining
 * Date: 2019/3/19
 * Time: 15:03
 */

namespace app\wcore;


 use Hyperf\Di\Annotation\Inject;
 use Hyperf\HttpServer\Contract\ResponseInterface;

 class Response extends WObject
{

    protected $defaultMsg = [
        200=>"success",
        400=>"缺少必要参数",
        403=>"签名错误",
        503=>"接口超时",
        500=>"内部错误",

    ];
    protected $logfile = "wcore";
    protected $status = "status";
    protected $message = "message";
    protected $data = "data";
    protected $httpStatus = 200;
    function __construct($logfile="",$status="",$message="",$data="")
    {
        $logfile && $this->logfile = $logfile;
        $status && $this->status = $status;
        $message && $this->message = $message;
        $data && $this->data = $data;
    }


    public function Format($data = "",$statusCode=200,$message = ""){
        $message = $message?:$this->defaultMsg[$statusCode];
        $responseData = [
            "$this->status"=>$statusCode,
            "$this->message"=>$message,
            "$this->data" =>$data
        ];
        $result = $this->toJson($responseData);
        Log::instant()->Write($responseData);
        return $result;
    }

     /**
      * 配置响应使用的类，这里用了laravel的response，其他框架可以重写这方法
      * @param $responseData
      * @return
      */
    public function toJson($responseData){
        $responseData = $this->responseinterface->json($responseData);
        return $responseData;
    }
    public function SetStatus($key){
        $this->status = $key;
        return $this;
    }
     public function SetMessage($key){
         $this->message = $key;
         return $this;
     }
     public function SetData($key){
         $this->data = $key;
         return $this;
     }
     public function SetLogFile($key){
         $this->logfile = $key;
         return $this;
     }
     public function SetDefaultMsg($arrMsg){
         $this->defaultMsg = $arrMsg;
         return $this;
     }
     public function SetHttpStatus($httpStatus){
        $this->httpStatus = $httpStatus;
        return $this;
     }
}
