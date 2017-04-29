<?php
define("ROOT",__DIR__ . "/..");
require ROOT . "/App/bootstrap.php";
ini_set("display_errors",config("app.display_errors"));
\Helpers\View::setPatternsFolder(ROOT . "/App/Views");
session_start();

require ROOT . "/App/route.php";