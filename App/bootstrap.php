<?php
if (file_exists(ROOT . "/.env")) {
    $lines = file(ROOT . "/.env");
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        putenv($line);
    }
}

function env($key,$default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }

    if (preg_match('~^"(.+)"$~',$value)) {
        return substr($value, 1, -1);
    }

    return $value;
}

spl_autoload_register(function ($className){
    $className = preg_replace('~\\\~','/',$className);
    require_once ROOT . "/App/" . $className . ".php";
});

function config($path, $default = null) {
   return Helpers\Config::get($path, $default);
}

function array_get($array,$path,$default = null)
{
    $path = explode(".",$path);
    $firstPath = array_shift($path);

    if (count($path) === 0) {
        return isset($array[$firstPath]) ? $array[$firstPath] : $default;
    } elseif (!isset($array[$firstPath])) {
        return $default;
    } else {
        return array_get(
            $array[$firstPath],
            implode(".",$path),
            $default
        );
    }
}

function array_set(&$array,$path,$value)
{
    if (!is_array($array)) {
        $array =[];
    }

    $path = explode(".",$path);
    $firstPath = array_shift($path);

    if (count($path) === 0) {
        $array[$firstPath] = $value;
    } else {
        $tmpArray = isset($array[$firstPath]) ? $array[$firstPath] : [];
        array_set(
            $tmpArray,
            implode(".",$path),
            $value
        );
        $array[$firstPath] = $tmpArray;
    }
    return $array;
}

