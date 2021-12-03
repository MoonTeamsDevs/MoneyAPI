<?php

namespace MoonTeam\MoneyAPI\commands\admin;

use MoonTeam\MoneyAPI\Main;
use MoonTeam\MoneyAPI\utils\Lang;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;

class RemoveMoney extends Command {

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission("money.removemoney")){
            if (!isset($args[1])){
                $sender->sendMessage("§cPlease do /removemoney [player] [money].");
                return;
            }else{
                if ($args[1] < 0){
                    $sender->sendMessage(Lang::get("invalid-number"));
                    return;
                }
                $player = Server::getInstance()->getPlayerByPrefix($args[0]);
                if ($player instanceof Player){
                    $provider = Main::getInstance()->getProvider();
                    if ($provider->getMoney($player) < $args[1]){
                        $sender->sendMessage(str_replace(["{player}", "{money}"], [$player->getName(), $args[1]], Lang::get("less-than")));
                        return;
                    }
                    $provider->removeMoney($player, $args[1]);
                    $sender->sendMessage(str_replace(["{player}", "{money}"], [$player->getName(), $args[1]], Lang::get("removemoney-sender-msg")));
                    $player->sendMessage(str_replace(["{player}", "{money}"], [$sender->getName(), $args[1]], Lang::get("removemoney-player-msg")));
                    return;
                }else{
                    $provider = Main::getInstance()->getProvider();
                    if ($provider->hasAccount($args[0])){
                        if ($provider->getMoney($args[0]) < $args[1]){
                            $sender->sendMessage(str_replace(["{player}", "{money}"], [$args[0], $args[1]], Lang::get("less-than")));
                            return;
                        }
                        $provider->removeMoney($args[0], $args[1]);
                        $sender->sendMessage(str_replace(["{player}", "{money}"], [$args[0], $args[1]], Lang::get("removemoney-sender-msg")));
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