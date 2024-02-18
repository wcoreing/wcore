<?php
/**
 * User:weining
 * Date:2021 2021/12/30
 * Time:下午 6:09
 *
 */

namespace app\wcore;

trait Design
{
    static public function Instant(...$data){
        static $instant = [];
        $get_called_class = get_called_class();
        if(isset($instant[$get_called_class])) return $instant[$get_called_class];
        $instant[$get_called_class] = new static(...$data);
        return $instant[$get_called_class];
    }
}
