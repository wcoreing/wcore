<?php
/**
 * Created by PhpStorm.
 * User: weining
 * Date: 2021/6/2
 * Time: 下午 8:00
 */

namespace app\wcore;

class Funcs extends WObject {
    /**
     * 从url中获取参数
     * @param $query
     * @return array
     */
   static function GetParamFromUrl($url,$key)
    {
        $arr = parse_url($url);
        $queryParts = explode('&', $arr["query"]);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params[$key];
    }

    /**
     * @param $data
     * @param $rule _2A下划线转驼峰 A2_驼峰转下划线
     */
    static function FormatKey($data,$rule){
        //获取值
        $getVal = function($r,$rule){
            $type = gettype($r);
            echo $type;
            switch ($type){
                case "array":
                case "object":
                    $rs = self::FormatKey($r,$rule);
                    break;
                case "integer":
                default:
                    $rs = $r;
                    break;
            }
            return $rs;
        };
        switch ($rule){
            case "_2A":
                $separator = "_";
                foreach ($data as $k=>$r){
                    if(is_numeric($k)){
                        $kNew = $k;
                    }else{
                        $uncamelized_words =  str_replace($separator, " ", $k);
                        if($uncamelized_words == $k) {
                            $kNew = lcfirst($k);
                        }else{
                            $newWorks =  lcfirst(ucwords($uncamelized_words));
                            $kNew = ltrim(str_replace(" ", "", $newWorks), $separator );
                        }
                    }
                    $result[$kNew] = $getVal($r,$rule);
                }
                break;
            case "A2_":
                $separator = "_";
                foreach ($data as $k=>$r){
                    $kNew = strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $k));
                    $result[$kNew] = $getVal($r,$rule);

                }
                break;
            default:
                $result = $data;
                break;
        }
        return $result;
    }
    //随机数
    function uuid(){
        $id = substr(strtotime(date("Y-m-d", time())), 0, 5) . substr(strrev(microtime()), -1, 5) . substr(mt_rand(), 0, 3) . substr(rand(), 0, 3);
        return $id;
    }

    function filterKey($data,$keys=[]){
        foreach ($data as $k=>$r){
            foreach ($keys as $key){
                $res[$k][$key] = $r[$key];
            }
        }
        return $res;
    }
    function ArraySetKey($data,$key){
        foreach ($data as $item){
            $result[$item[$key]] = $item;
        }
        return $result;
    }

    function ArrayGetValueByKey($data,$key){
        foreach ($data as $item){
            $result[] = $item[$key];
        }
        return $result;
    }
}
