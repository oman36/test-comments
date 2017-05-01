<?php
try {

    define("ROOT",__DIR__ . "/..");
    require ROOT . "/App/bootstrap.php";
    ini_set("display_errors",config("app.display_errors"));
    \Helpers\View::setPatternsFolder(ROOT . "/App/Views");
    session_start();

    require ROOT . "/App/route.php";

} catch (\Exception $e) {
    if (env("DEBUG")) {
        http_response_code(500);
        echo "<pre>";
        echo "<b>" . $e->getMessage(),"<b>\n";
        throw $e;
    } else {
        http_response_code(500);
    }
}