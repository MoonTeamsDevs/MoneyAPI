<?php

namespace MoonTeam\MoneyAPI\providers;

use MoonTeam\MoneyAPI\Main;
use MoonTeam\MoneyAPI\tasks\async\SQLITEAsyncTask;
use MoonTeam\MoneyAPI\utils\Utils;
use pocketmine\player\Player;
use pocketmine\Server;

class SQLITEProvider implements Provider {

    public function __construct()
    {
        $this->initTable();
    }

    public function getData(): \SQLite3{
        return new \SQLite3(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"));
    }

    public function initTable(): void{
        $this->getData()->query("CREATE TABLE IF NOT EXISTS `players` (`pseudo` VARCHAR(55), `money` FLOAT NOT NULL, PRIMARY KEY(`pseudo`))");
    }

    public function hasAccount(Player|string $player): bool
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                return isset(Utils::$cachedPlayers[$player->getName()]);
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player->getName() . "'");
                return $query->numColumns() > 0;
            }
        }else{
            if (Utils::caching()){
                return isset(Utils::$cachedPlayers[$player]);
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player . "'");
                return $query->numColumns() > 0;
            }
        }
    }

    public function createAccount(Player|string $player): void
    {
        $money_start = Main::getInstance()->getConfig()->get("money-start");
        if ($player instanceof Player){
            Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "INSERT INTO `players` (`pseudo`, `money`) VALUES ('" . $player->getName() . "', '" . $money_start . "')", function (SQLITEAsyncTask $asyncTask, Server $server) use ($player, $money_start){
                if (Utils::caching()){
                    Utils::$cachedPlayers[$player->getName()] = [
                        "money" => $money_start
                    ];
                }
            }));
        }else{
            Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "INSERT INTO `players` (`pseudo`, `money`) VALUES ('" . $player . "', '" . $money_start . "')", function (SQLITEAsyncTask $asyncTask, Server $server) use ($player, $money_start){
                if (Utils::caching()){
                    Utils::$cachedPlayers[$player->getName()] = [
                        "money" => $money_start
                    ];
                }
            }));
        }
    }

    public function setMoney(Player|string $player, float $money): void
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                Utils::$cachedPlayers[$player->getName()]["money"] = $money;
            }else{
                Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "UPDATE `players` SET money='$money' WHERE pseudo='" . $player->getName() . "'"));
            }
        }else{
            if (Utils::caching()){
                Utils::$cachedPlayers[$player]["money"] = $money;
            }else{
                Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "UPDATE `players` SET money='$money' WHERE pseudo='" . $player . "'"));
            }
        }
    }

    public function addMoney(Player|string $player, float $money): void
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                $money_actus = Utils::$cachedPlayers[$player->getName()]["money"];
                Utils::$cachedPlayers[$player->getName()]["money"] = $money_actus + $money;
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player->getName() . "'");
                $money_actus = $query->fetchArray(SQLITE3_ASSOC)["money"];
                Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "UPDATE `players` SET money='" . ($money_actus + $money) . "' WHERE pseudo='" . $player->getName() . "'"));
            }
        }else{
            if (Utils::caching()){
                $money_actus = Utils::$cachedPlayers[$player]["money"];
                Utils::$cachedPlayers[$player]["money"] = $money_actus + $money;
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player . "'");
                $money_actus = $query->fetchArray(SQLITE3_ASSOC)["money"];
                Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "UPDATE `players` SET money='" . ($money_actus + $money) . "' WHERE pseudo='" . $player . "'"));
            }
        }
    }

    public function removeMoney(Player|string $player, float $money): void
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                $money_actus = Utils::$cachedPlayers[$player->getName()]["money"];
                Utils::$cachedPlayers[$player->getName()]["money"] = $money_actus - $money;
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player->getName() . "'");
                $money_actus = $query->fetchArray(SQLITE3_ASSOC)["money"];
                Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "UPDATE `players` SET money='" . ($money_actus - $money) . "' WHERE pseudo='" . $player->getName() . "'"));
            }
        }else{
            if (Utils::caching()){
                $money_actus = Utils::$cachedPlayers[$player]["money"];
                Utils::$cachedPlayers[$player]["money"] = $money_actus - $money;
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player . "'");
                $money_actus = $query->fetchArray(SQLITE3_ASSOC)["money"];
                Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "UPDATE `players` SET money='" . ($money_actus - $money) . "' WHERE pseudo='" . $player . "'"));
            }
        }
    }

    public function getMoney(Player|string $player): float
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                return Utils::$cachedPlayers[$player->getName()]["money"];
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player->getName() . "'");
                return $query->fetchArray(SQLITE3_ASSOC)["money"];
            }
        }else{
            if (Utils::caching()){
                return Utils::$cachedPlayers[$player]["money"];
            }else{
                $query = $this->getData()->query("SELECT * FROM `players` WHERE pseudo='" . $player . "'");
                return $query->fetchArray(SQLITE3_ASSOC)["money"];
            }
        }
    }

    public function savePlayersData()
    {
        foreach (Utils::$cachedPlayers as $player => $value){
            $data = Utils::$cachedPlayers[$player];
            Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncTask(Main::getInstance()->getDataFolder() . Main::getInstance()->getConfig()->get("sqlite-db"), "UPDATE `players` SET money='" . $data["money"] . "' WHERE pseudo='" . $player . "'"));
        }
    }

}