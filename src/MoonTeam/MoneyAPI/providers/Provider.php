<?php

namespace MoonTeam\MoneyAPI\providers;

use pocketmine\player\Player;

interface Provider {

    public function hasAccount(Player|string $player):bool;

    public function createAccount(Player|string $player): void;

    public function setMoney(Player|string $player, float $money): void;

    public function addMoney(Player|string $player, float $money): void;

    public function removeMoney(Player|string $player, float $money): void;

    public function getMoney(Player|string $player): float;

    public function savePlayersData();

}