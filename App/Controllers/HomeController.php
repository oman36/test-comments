<?php
/**
 * Created by PhpStorm.
 * User: neoman
 * Date: 29.04.17
 * Time: 20:54
 */

namespace Controllers;


class HomeController extends BaseController
{
    public function index()
    {
        echo "Index";
    }

    public function error404()
    {
        header("HTTP/1.x 404 Not Found");
        echo "404";
    }
}