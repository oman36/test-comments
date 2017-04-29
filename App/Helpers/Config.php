<?php
namespace Helpers;
/**
 * Created by PhpStorm.
 * User: neoman
 * Date: 29.04.17
 * Time: 21:31
 */
class Config
{
    protected static $_config = [];
    protected static $_loaded = false;

    static function get($path, $default = null)
    {
        if (!self::$_loaded) {
            self::$_config = require_once ROOT . "/App/config.php";
            self::$_loaded = true;
        }

        return array_get(self::$_config,$path,$default);
    }
}