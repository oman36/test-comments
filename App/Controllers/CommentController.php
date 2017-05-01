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

class CommentController extends BaseController
{
    public function store()
    {
        $data = $_POST;
        $errors = [];
        foreach ($data as $key => &$value) {
            $value = preg_replace('~(^\s+|\s+$)~','',$value);
            switch ($key) {
                case "comment" :
                    if (empty($value)) {
                        $errors[$key] = "Необходимо заполнить комментарий";
                        continue;
                    }
                    break;
                case "author" :
                    if (empty($value)) {
                        $errors[$key] = "Необходимо заполнить поле автора";
                        continue;
                    }
                    break;
                case "parent_id" :
                    if (! $value = filter_var($value,FILTER_VALIDATE_INT)){
                        $errors[$key] = "Некорректный ИД комментария";
                        continue;
                    }
                    break;
                default :
                    unset($data[$key]);
                    break;
            }
        }
        unset($value);

        if (!empty($errors)) {
            return View::json($errors,400);
        }

        $data['created_at'] = date("Y-m-d H:i:s");
        $comment = Comment::insert($data);

        return View::json([
            "id" => $comment->id,
        ],201);
    }

    public function error404()
    {
        header("HTTP/1.x 404 Not Found");
        echo "404";
    }
}