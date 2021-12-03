<?php

namespace MoonTeam\MoneyAPI\tasks\async;

use MoonTeam\MoneyAPI\utils\Utils;
use pocketmine\scheduler\AsyncTask;

class SQLITEAsyncCache extends AsyncTask {

    private string $file_name;

    public function __construct(string $file_name)
    {
        $this->file_name = $file_name;
    }

    public function onRun(): void
    {
        $db = new \SQLite3($this->file_name);
        $query = $db->query("SELECT * FROM `players`");
        $array = [];
        if ($query !== null){
            foreach ($query->fetchArray() as $value){
                var_dump($value);
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