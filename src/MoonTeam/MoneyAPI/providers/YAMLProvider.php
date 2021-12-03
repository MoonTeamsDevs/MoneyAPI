<?php

namespace MoonTeam\MoneyAPI\providers;

use MoonTeam\MoneyAPI\Main;
use MoonTeam\MoneyAPI\utils\Utils;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class YAMLProvider implements Provider {

    public function getPlayersData(): Config{
        if (!file_exists(Main::getInstance()->getDataFolder() . "players.yml")){
            Main::getInstance()->saveResource("players.yml");
        }
        return new Config(Main::getInstance()->getDataFolder() . "players.yml", Config::JSON);
    }

    public function hasAccount(Player|string $player): bool
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                return isset(Utils::$cachedPlayers[$player->getName()]);
            }else{
                return $this->getPlayersData()->exists($player->getName());
            }
        }else{
            if (Utils::caching()){
                return isset(Utils::$cachedPlayers[$player]);
            }else{
                return $this->getPlayersData()->exists($player);
            }
        }
    }

    public function createAccount(Player|string $player): void
    {
        if ($player instanceof Player){
            $config = $this->getPlayersData();
            $config->set($player->getName(), ["money" => Main::getInstance()->getConfig()->get("money-start")]);
            $config->save();
            if (Utils::caching()){
                Utils::$cachedPlayers[$player->getName()] = [
                    "money" => Main::getInstance()->getConfig()->get("money-start")
                ];
            }
        }else{
            $config = $this->getPlayersData();
            $config->set($player, ["money" => Main::getInstance()->getConfig()->get("money-start")]);
            $config->save();
            if (Utils::caching()){
                Utils::$cachedPlayers[$player] = [
                    "money" => Main::getInstance()->getConfig()->get("money-start")
                ];
            }
        }
    }

    public function setMoney(Player|string $player, float $money): void
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                Utils::$cachedPlayers[$player->getName()]["money"] = $money;
            }else{
                $config = $this->getPlayersData();
                $all = $config->get($player->getName());
                $all["money"] = $money;
                $config->set($player->getName(), $all);
                $config->save();
            }
        }else{
            if (Utils::caching()){
                Utils::$cachedPlayers[$player]["money"] = $money;
            }else{
                $config = $this->getPlayersData();
                $all = $config->get($player);
                $all["money"] = $money;
                $config->set($player, $all);
                $config->save();
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
                $config = $this->getPlayersData();
                $all = $config->get($player->getName());
                $all["money"] = $all["money"] + $money;
                $config->set($player->getName(), $all);
                $config->save();
            }
        }else{
            if (Utils::caching()){
                $money_actus = Utils::$cachedPlayers[$player]["money"];
                Utils::$cachedPlayers[$player]["money"] = $money_actus + $money;
            }else{
                $config = $this->getPlayersData();
                $all = $config->get($player);
                $all["money"] = $all["money"] + $money;
                $config->set($player, $all);
                $config->save();
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
                $config = $this->getPlayersData();
                $all = $config->get($player->getName());
                $all["money"] = $all["money"] - $money;
                $config->set($player->getName(), $all);
                $config->save();
            }
        }else{
            if (Utils::caching()){
                $money_actus = Utils::$cachedPlayers[$player]["money"];
                Utils::$cachedPlayers[$player]["money"] = $money_actus - $money;
            }else{
                $config = $this->getPlayersData();
                $all = $config->get($player);
                $all["money"] = $all["money"] - $money;
                $config->set($player, $all);
                $config->save();
            }
        }
    }

    public function getMoney(Player|string $player): float
    {
        if ($player instanceof Player){
            if (Utils::caching()){
                return Utils::$cachedPlayers[$player->getName()]["money"];
            }else{
                return $this->getPlayersData()->get($player->getName())["money"];
            }
        }else{
            if (Utils::caching()){
                return Utils::$cachedPlayers[$player]["money"];
            }else{
                return $this->getPlayersData()->get($player)["money"];
            }
        }
    }

    public function savePlayersData()
    {
        foreach (Utils::$cachedPlayers as $player => $value){
            $config = $this->getPlayersData();
            $config->set($player, $value);
            $config->save();
        }
    }

}