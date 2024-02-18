<?php

namespace app\wcore;


use Illuminate\Support\Str;

/**
 * by weining 2021/12/16
 */
class Log extends WObject
{
    //文件名
    protected $logfile ;
    protected $logfileReal;
    protected $request_id;
    protected $dir;
    public function __construct($dir,$logfile)
    {
        $this->request_id = $this->generateRequestId();
        $this->dir = $dir;
        $this->SetLogFile($this->dir.DIRECTORY_SEPARATOR.$logfile);
    }
    public function SetLogFile($logfile,$format=true){
        $this->logfile = $logfile;
        $this->logfileReal = $format==true?$this->createFile($logfile):$logfile;
        return $this;
    }
    public function getLogFile(){
        return $this->logfile;
    }
    public function Write($data,$logfile=""){
        $formatContent = $this->fomat($data);
//        $this->writeStd($formatContent);
        $rs = $this->writeFile($formatContent,$logfile);
        return $rs;
    }

    protected function fomat($content){
        $result  = [];
        if(is_array($content)){
            $result[] = json_encode($content,JSON_UNESCAPED_UNICODE);
            $result = implode(",",$result);
        }else{
            $result = $content;
        }
        $rs = "[".date("Y-m-d H:i:s")."] [".$this->request_id."] ".$result;
        return $rs;
    }
    protected function writeFile($formatContent,$logfile=""){
        if($logfile){
            $logfile = $this->createFile($this->dir.DIRECTORY_SEPARATOR.$logfile);
        }else{
            $logfile = $this->logfileReal;
        }

        $stream = fopen($logfile, 'a');
        fwrite($stream, $formatContent."\n");
        fclose($stream);
        return $stream;
    }

    protected function writeStd($formatContent){
        $fh = fopen('php://stdout', 'w');
        fwrite($fh, $formatContent."\n");
        fclose($fh);
    }

    protected function createFile($logfile){
        $pos = strrpos($logfile,"/");
        if($pos !== false){
            $dir = substr($logfile,0,$pos);
            if(!is_dir($dir)){
                FileSystem::instant()->CreateDir($dir);
            }
        }
        return  $this->makeFileName($logfile);
    }
    protected function makeFileName($fileName){
        return  $fileName."-".date("Ymd").".log";
    }

    /**
     * 生成request_id
     * @return string
     */
    protected function generateRequestId() {
        return (string)Str::orderedUuid();
    }
}
