<?php

namespace MoonTeam\MoneyAPI\tasks\async;

use MoonTeam\MoneyAPI\utils\Utils;
use pocketmine\scheduler\AsyncTask;

class MySQLAsyncCache extends AsyncTask {

    private array $mysql;

    public function __construct(array $mysql)
    {
        $this->mysql = $mysql;
    }

    public function onRun(): void
    {
        $database = $this->mysql;
        $db = new \mysqli($database["host"], $database["username"], $database["password"], $database["database"], $database["port"]);
        $query = $db->query("SELECT * FROM `players`");
        $array = [];
        if ($query !== null){
            foreach ($query->fetch_all() as $value){
                $array[$value[0]] = [
                    "money" => $value[1]
                ];
            }
            $db->close();
            $this->setResult($array);
        }else{
            $db->close();
            $this->cancelRun();
        }
    }

    public function onCompletion(): void
    {
        Utils::$cachedPlayers = $this->getResult();
    }

}