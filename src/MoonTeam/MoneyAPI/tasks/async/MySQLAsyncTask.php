<?php

namespace MoonTeam\MoneyAPI\tasks\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class MySQLAsyncTask extends AsyncTask {

    private array $mysql;
    private string $query;
    private $call;

    public function __construct(array $mysql, string $query, callable $call = null)
    {
        $this->mysql = $mysql;
        $this->query = $query;
        $this->call = $call;
    }

    public function onRun(): void
    {
        $database = $this->mysql;
        $db = new \mysqli($database["host"], $database["username"], $database["password"], $database["database"], $database["port"]);
        $query = $db->query($this->query);
        if ($query !== null){
            $db->close();
        }else{
            $db->close();
            $this->cancelRun();
        }
    }

    public function onCompletion(): void
    {
        if ($this->call !== null){
            call_user_func($this->call, $this, Server::getInstance());
        }
    }

}