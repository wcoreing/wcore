<?php
/**
 * User:weining
 * Date:2022 2022/4/1
 * Time:下午 6:03
 *
 */

namespace app\wcore;



use app\wcore\DB\EloquentDB;

class Provider extends WObject
{
    public function boot(){
        Log::Instant(LOG_PATH,"request/log");
        $this->displayError();
        Validator::Instant(WException::class);
        EloquentDB::Instant();
    }
    private function displayError(){
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('display_errors', '1');
        if($_ENV["APP_DEBUG"] == "false"){
            set_error_handler([$this,"customErrorHandler"]);
        }
    }
    public function customErrorHandler($severity, $message, $file, $line)
    {

        if (error_reporting() & $severity) {
            // 错误信息记录到日志文件
            $msg = "[$severity] $message in $file on line $line";
            Log::Instant()->Write($msg,"exception/log");
        }
    }
}
