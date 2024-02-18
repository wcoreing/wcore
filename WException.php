<?php

namespace app\wcore;
class WException extends \Exception
{
    public $httpCode = 200;
    public $data = [];
    public function response(){
        $contents = [
            "code"=>$this->getCode(),
            "msg"=>$this->getMessage(),
            "data"=>$this->data
        ];
        Log::Instant()->Write($contents);
        return response($contents,$this->httpCode,[],"json");
    }
}
