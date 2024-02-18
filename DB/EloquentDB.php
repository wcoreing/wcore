<?php

namespace app\wcore\DB;

use app\wcore\Log;
use app\wcore\WObject;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Events\QueryExecuted;
class EloquentDB extends WObject
{
    private  $capsule;
    private $container;
    function __construct()
    {
        $this->container = new Container();
        $this->capsule = new Manager($this->container);
        $this->capsule->setEventDispatcher(new Dispatcher());
        foreach (C("connections") as $key => $connection){
            $this->capsule->addConnection([
                'driver' => $connection["driver"],
                'host' => $connection["host"],
                'database' => $connection["database"],
                'username' =>$connection["username"],
                'password' => $connection["password"],
                'charset' => $connection["charset"],
                'collation' => $connection["collation"],
                'prefix' =>  $connection["prefix"],
            ],$key);

        }
        $this->capsule->setAsGlobal();

        $this->capsule->bootEloquent();


            // 开启查询日志
            $this->capsule->getConnection("default")->enableQueryLog();
            // 注册查询执行事件监听器
            $this->capsule->getConnection("default")->listen(function (QueryExecuted $query) {
                // 记录查询日志
                $sql = $query->sql;
                $bindings = $query->bindings;
                $time = $query->time;
                Log::Instant()->Write($sql . ' [' . implode(', ', $bindings) . '] - ' . $time . 'ms' . PHP_EOL, 'sql/query' );

            });


    }

    function DB($db="default")
    {
        return Manager::connection($db);
    }
}