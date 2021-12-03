<?php

namespace MoonTeam\MoneyAPI\listeners;

use MoonTeam\MoneyAPI\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;

class PlayerListener implements Listener {

    public function onPreLogin(PlayerPreLoginEvent $event){
        $player = $event->getPlayerInfo();
        $provider = Main::getInstance()->getProvider();
        if (!$provider->hasAccount($player->getUsername())){
            $provider->createAccount($player->getUsername());
        }
    }

}