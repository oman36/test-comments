<?php

namespace Models;

/**
 * Class Model
 * @package Models
 *
 * @property int $id
 * @property string $author
 * @property string $comment
 * @property int $parent_id
 * @property string $created_at
 */
class Comment extends Model
{
    public $table = "comments";

    /**
     * @return self[]
     */
    public function getChildren()
    {
        $children = self::query()
            ->where("parent_id","=",$this->id)
            ->get();

        if (empty($children)) {
            return [];
        }
        foreach ($children as &$child) {
            $child = new self($child);
        }

        return $children;
    }
}