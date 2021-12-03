<?php

namespace MoonTeam\MoneyAPI\commands\all;

use MoonTeam\MoneyAPI\utils\Lang;
use MoonTeam\MoneyAPI\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;

class Money extends Command {

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission("money.money")){
            if (!isset($args[0])){
                if ($sender instanceof Player){
                    $provider = Main::getInstance()->getProvider();
                    $sender->sendMessage(str_replace(["{money}"], [$provider->getMoney($sender)], Lang::get("view-money")));
                    return;
                }
                $sender->sendMessage("§cPlease do /money [player].");
                return;
            }else{
                $player = Server::getInstance()->getPlayerByPrefix($args[0]);
                if ($player instanceof Player){
                    $provider = Main::getInstance()->getProvider();
                    $sender->sendMessage(str_replace(["{money}", "{player}"], [$provider->getMoney($player), $player->getName()], Lang::get("view-money-player")));
                    return;
                }else{
                    $provider = Main::getInstance()->getProvider();
                    if ($provider->hasAccount($args[0])){
                        $sender->sendMessage(str_replace(["{money}", "{player}"], [$provider->getMoney($args[0]), $args[0]], Lang::get("view-money-player")));
                        return;
                    }else{
                        $sender->sendMessage(Lang::get("player-no-in-db"));
                        return;
                    }
                }
            }
        }else{
            $sender->sendMessage(Lang::get("no-permission"));
            return;
        }
    }

}