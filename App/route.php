<?php
$uri = $_SERVER['REQUEST_URI'];
$controller = "Home";
$action = "index";

class Exception404 extends Exception
{
}

function callRoute($controller,$action = "index",$params = []) {
    $controller = "\\Controllers\\" . $controller . "Controller";
    if (!in_array($action,get_class_methods($controller))) {
        throw new Exception404("404");
    }
    call_user_func_array([$controller,$action],$params);
}

try {
    if ("/" === $uri) {
        callRoute($controller, $action);
    } elseif ("/comment" === $uri && "POST" === $_SERVER['REQUEST_METHOD']) {
        callRoute("Comment", "store");
    } elseif ("/test" === $uri) {
        \Helpers\View::json([
            'name' => "Fatal",
            'comment' => "Error",
        ],400);
    } else {
        callRoute("Home", "error404");
    }
} catch (Exception404 $exception) {
    callRoute("Home", "error404");
}
