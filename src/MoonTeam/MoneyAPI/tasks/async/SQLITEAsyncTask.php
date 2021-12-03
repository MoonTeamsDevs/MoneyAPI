<?php

namespace MoonTeam\MoneyAPI\tasks\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SQLITEAsyncTask extends AsyncTask {

    private string $query;
    private string $file_name;
    private $call;

    public function __construct(string $file_name, string $query, callable $call = null)
    {
        $this->file_name = $file_name;
        $this->query = $query;
        $this->call = $call;
    }

    public function onRun(): void
    {
        $db = new \SQLite3($this->file_name);
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