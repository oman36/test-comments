<?php
/**
 * Created by PhpStorm.
 * User: neoman
 * Date: 29.04.17
 * Time: 20:54
 */

namespace Controllers;

use Helpers\View;
use Models\Comment;

class HomeController extends BaseController
{
    public function index()
    {
        $comments = Comment::getOnlyWithoutParents()
            ->orderBy("created_at","DESC")
            ->limit(20)
            ->get();

        foreach ($comments as $i => $comment) {
            $comments[$i] = Comment::prepareChildren($comment);
        }
        return View::render("home/index",[
            "comments" => $comments
        ]);
    }

    public function error404()
    {
        header("HTTP/1.x 404 Not Found");
        echo "404";
    }
}