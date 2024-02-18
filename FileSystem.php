<?php
namespace app\wcore;


use Exception;

class FileSystem extends WObject
{
    protected $filePermission = 0777;
    public function CreateDir($dir,$mod=0777){
        if(!is_dir($dir)){
            $status =  mkdir($dir,$mod,true);
            if (false === $status && !is_dir($dir)) {
                throw new Exception($dir."创建目录失败");
            }
            chmod($dir,$mod);
        }
        return true;
    }
    public function CreateFile($filePath){
        if(is_file($filePath)) return true;
        $dir = dirname($filePath);
        self::CreateDir($dir);
        $stream = fopen($filePath, 'w');
        if(!is_resource($stream)){
            throw new Exception($dir."创建文件失败：$filePath");
        }
        if ($this->filePermission !== null) {
            @chmod($filePath, $this->filePermission);
        }
        return $stream;
    }
    public function FindFile($dir,$file,&$res){
        if(!is_dir($dir)) Validator::Instant()->ThrowException("目录不存在$dir");
        $data = scandir($dir);
        foreach ($data as $val){
            if($file == $val){
                $res[] = $dir."/".$file;
            }else if(is_dir($subdir = $dir."/".$val)){
                if(in_array($val,[".",".."]))continue;
                static::FindFile($subdir,$file,$res);
            }
        }
    }
}
