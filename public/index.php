<?php
define("ROOT",__DIR__ . "/..");
require ROOT . "/App/bootstrap.php";
ini_set("display_errors",config("app.display_errors"));
session_start();

require ROOT . "/App/route.php";