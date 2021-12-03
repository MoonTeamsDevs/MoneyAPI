<?php

namespace MoonTeam\MoneyAPI;

use JetBrains\PhpStorm\Pure;
use MoonTeam\MoneyAPI\commands\admin\AddMoney;
use MoonTeam\MoneyAPI\commands\admin\RemoveMoney;
use MoonTeam\MoneyAPI\commands\admin\SetMoney;
use MoonTeam\MoneyAPI\commands\all\Money;
use MoonTeam\MoneyAPI\listeners\PlayerListener;
use MoonTeam\MoneyAPI\providers\JSONProvider;
use MoonTeam\MoneyAPI\providers\MySQLProvider;
use MoonTeam\MoneyAPI\providers\SQLITEProvider;
use MoonTeam\MoneyAPI\providers\YAMLProvider;
use MoonTeam\MoneyAPI\tasks\async\MySQLAsyncCache;
use MoonTeam\MoneyAPI\tasks\async\SQLITEAsyncCache;
use MoonTeam\MoneyAPI\utils\Utils;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Main extends PluginBase {

    public static self $instance;
    private $provider;

    public static function getInstance(): self{
        return self::$instance;
    }

    protected function onEnable(): void
    {
        self::$instance = $this;
        Utils::$cache = $this->getConfig()->get("cache");

        $this->initProvider();
        if (Utils::caching()){
            $this->initCache();
        }
        $this->initCommands();

        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
    }

    protected function onDisable(): void
    {
        if (Utils::caching()) {
            $provider = $this->getProvider();
            $provider->savePlayersData();
        }
    }

    private function initProvider(){
        $provider = $this->getConfig()->get("provider");
        switch ($provider){
            case "mysql":
                $db = $this->getConfig()->get("database");
                Utils::$mysql = [
                    "host" => $db["host"],
                    "username" => $db["username"],
                    "password" => $db["password"],
                    "database" => $db["database"],
                    "port" => $db["port"]
                ];
                $this->provider = new MySQLProvider();
                break;
            case "sql":
                $this->provider = new SQLITEProvider();
                break;
            case "json":
                $this->provider = new JSONProvider();
                break;
            case "yaml":
                $this->provider = new YAMLProvider();
                break;
        }
    }

    private function initCache(): void{
        $provider = $this->getProvider();
        if ($provider instanceof MySQLProvider){
            Server::getInstance()->getAsyncPool()->submitTask(new MySQLAsyncCache(Utils::$mysql));
        }
        if ($provider instanceof SQLITEProvider){
            Server::getInstance()->getAsyncPool()->submitTask(new SQLITEAsyncCache($this->getDataFolder() . $this->getConfig()->get("sqlite-db")));
        }
        if ($provider instanceof YAMLProvider || $provider instanceof JSONProvider){
            foreach ($provider->getPlayersData() as $player => $value){
                Utils::$cachedPlayers[$player] = $value;
            }
        }
    }

    private function initCommands(): void{
        $this->getServer()->getCommandMap()->registerAll("MoneyAPI", [
            new Money("money", "Allows you to see your or a player's money.", "money", []),
            new SetMoney("setmoney", "Allows you to redefine a player's money.", "setmoney [player] [money]", []),
            new AddMoney("addmoney", "Allows you to add money to a player.", "addmoney [player] [money]", []),
            new RemoveMoney("removemoney", "Allows you to withdraw money from a player.", "removemoney, [player] [money]", [])
        ]);
    }

    public function getProvider(): MySQLProvider|SQLITEProvider|YAMLProvider|JSONProvider{
        return $this->provider;
    }

}