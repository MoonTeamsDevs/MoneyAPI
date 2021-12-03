<?php

namespace MoonTeam\MoneyAPI\utils;

class Utils {

    public static array $cachedPlayers = [];
    public static array $mysql = [];
    public static bool $cache = false;

    public static function caching(): bool{
        return self::$cache;
    }

}