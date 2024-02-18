<?php
/**
 * User:weining
 * Date:2021 2021/12/30
 * Time:下午 6:10
 *
 */

namespace app\wcore;
use think\Model;

class WModel extends Model
{
    use Design;
    public function fillData($formData,$fillable=[]){
        $fillable = $fillable?:$this->fillable;
        foreach ($fillable as $k=>$v){
            if(is_string($k)){
                isset($formData[$k]) && $res[$v] = $formData[$k];

            }else{
                isset($formData[$v]) && $res[$v] = $formData[$v];
            }
        }
        return $res;
    }

}
