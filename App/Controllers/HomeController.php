<?php
/**
 * Created by PhpStorm.
 * User: neoman
 * Date: 29.04.17
 * Time: 20:54
 */

namespace Controllers;

use Helpers\View;

class HomeController extends BaseController
{
    public function index()
    {
        return View::render("home/index",[]);
    }

    public function error404()
    {
        header("HTTP/1.x 404 Not Found");
        echo "404";
    }
}