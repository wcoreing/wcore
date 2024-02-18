<?php
/**
 * weining by
 */
namespace app\wcore;


use ReflectionFunction;

class Validator extends WObject
{
    public $defaultErr =[
        "required"=>'$field required'
    ];
    public $errs = [];
    public $exception ;
    public $code = 201;
    public $data = [];
    public $result = [];
    public function __construct($exception = ""){
        $this->exception=$exception?:WException::class;
    }

    public function ValidateArr($data,$fieldsItems){
        $this->data = $data;
        foreach ($fieldsItems as $fieldKey=>$rules){
            $inputVal = $data[$fieldKey]??null;
            if(isset($data[$fieldKey])){
                $this->result[$fieldKey]= $inputVal;
            }
            if(!is_array($rules)) self::ThrowException(400,"ValidateArr的规则需要是数组");
            foreach ($rules as $err => $rule){
                if(is_callable($rule)){
                    $rRule = new ReflectionFunction($rule);
                    $paramValues = [];
                    foreach ($rRule->getParameters() as $r){
                        $paramValues[$r->getName()] = $data[$r->getName()];
                    }
                    if($rRule->invokeArgs($paramValues)){
                        $this->errs[$fieldKey]= $err;
                    }
                }else{
                    $RuleMethod = $this->createRule($rule);
                    if(is_string($rule) && method_exists($this,$RuleMethod )){
                        if($this->$RuleMethod($fieldKey,$inputVal,$rule)){
                            $this->errs[$fieldKey]= $this->createErr($fieldKey,$rule,$err);
                        }
                    }
                }
            }

        }
        return $this;
    }
    public function Validate($inputVal,$rules=[]){

        foreach ($rules as $err => $rule){
            if(!$err){
                $this->ThrowException("错误信息不能为空");
            }
            if(is_callable($rule)){
                if($rule($inputVal)){
                    $this->errs[]= $err;
                }
            }else{
                $RuleMethod = $this->createRule($rule);
                if(is_string($rule) && method_exists($this, $RuleMethod)){
                    if($this->$RuleMethod("",$inputVal,$rule)){
                        $this->errs[]= $err;
                    }
                }
            }
        }
        return $this;
    }
    public function ThrowException($msg="",$code = "2001"){
        if(!$this->errs){
            $result = $this->result;
            $this->result = [];
            return $result;
        }
        if(!$msg){
            $msg = implode(",",$this->errs);
        }
        alertMsg($code,$msg,$this->data);
    }

    public function GetErrs(){
        return $this->errs;
    }

    public function SetCode($code){
        $this->code = $code;
    }
    private function ruleRequired($fieldKey,$inputVal,$rule){
        if(is_array($inputVal)){
            $rs = count($inputVal)==0;
        }else{
            $rs = $inputVal == "";
        }
        return $rs;
    }
    private function ruleDefault($fieldKey,$inputVal,$rule){
        if(empty($inputVal)){
            $rule = explode("|",$rule);
            $this->result[$fieldKey] = $rule[1];
        }
        return false;
    }
    private function createErr($fieldKey,$rule,$err){
        $result = $err?:$this->defaultErr[$rule];
        $result = str_replace('$field',$fieldKey,$result);
        return $result;
    }
    private function createRule($rule){
        $rules = explode("|",$rule);
        $method = "rule".$rules[0];
        return $method;
    }
}
