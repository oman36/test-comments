<?php

namespace Helpers;

/**
 * Class View
 * @package Helpers
 */
class View
{
    /**
     * Ассоциативный массив со всеми переменными
     *
     * @var array
     */
    protected static $_globalVars = [];

    /**
     * Флаг, который говорит о том, что сбор вывода начался.
     *
     * @var bool
     */
    protected static $_obStarted = false;

    protected static $_patternsFolder;

    protected static $_extendsStack = [];

    protected static $_activeSection = false;

    protected static $_renderLevel = 0;

    /**
     * @param string $folder
     */
    public static function setPatternsFolder($folder)
    {
        if (!preg_match('~/^~',$folder)) {
            $folder .= "/";
        }
        self::$_patternsFolder = $folder;
    }

    /**
     * Основная фунция рендера. Она так же будет доступна из
     * шаблона для подключения кусков типа виджетов
     *
     * @param string $pattern адресс относительно app/views2/
     * @param array $vars ассоциативный массив
     * @param int $response_code
     *
     * @return null
     *
     * @throws \Exception
     *
     */
    public static function render($pattern, $vars = [], $response_code = 200)
    {
        http_response_code($response_code);
        echo self::make($pattern,$vars);
        return null;
    }

    /**
     * Нужна функция для безопасного вывода (против скриптов и иньекций)
     *
     * @param $var
     */
    public static function decode($var)
    {
    }

    /**
     * Лишь объявляет какой шаблон будет расширен
     * (в какую переменную будет собираться то что вышло вэтом блоке)
     *
     * Нужно использовать стек для вложности расширений
     *
     * @param string $pattern
     */
    public static function extend($pattern)
    {
        // check stack trace last call.
        self::$_extendsStack[] = $pattern;
    }

    /**
     * все что вылетает после этого объявления
     * идет self::$_globalVars,
     * под ключем, который получает в параметре $name
     *
     * @var string $name
     * @throws \Exception
     *
     * @return bool
     */
    public static function startSection($name)
    {
        if (self::$_activeSection) {
            throw new \Exception('Section border error.');
        }

        self::$_activeSection = $name;
        ob_start();
        return true;
    }

    /**
     * завершает действие self::startSection();
     *
     * @var string $name
     * @throws \Exception
     *
     * @return bool
     */
    public static function endSection($name)
    {
        if ($name !== self::$_activeSection) {
            throw new \Exception('Section border error.');
        }

        self::$_activeSection = false;
        self::$_globalVars[$name] = ob_get_contents();
        ob_end_flush();
        return true;
    }

    /**
     * @param string $pattern
     * @param array $vars
     * @return string
     * @throws \Exception
     */
    public static function make($pattern,$vars = [])
    {
        self::$_renderLevel++;

        if (!is_array($vars)) {
            throw new \Exception("Vars need be array");
        }

        extract(array_merge(self::$_globalVars, $vars));
        ob_start();

        require self::$_patternsFolder . "{$pattern}.phtml";

        if (self::$_renderLevel === (count(self::$_extendsStack) + 1)) {
            $tmp = ob_get_contents();
            ob_clean();
            self::$_renderLevel--;
            return $tmp;
        }

        ob_end_flush();

        $tmp = self::make(array_shift(self::$_extendsStack),$vars);
        self::$_renderLevel--;
        return $tmp;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public static function setVar($name,$value)
    {
        self::$_globalVars[$name] = $value;
    }

    static function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header("Content-Type: text/json; charset=utf-8");
        echo json_encode($data);
        return true;
    }
}