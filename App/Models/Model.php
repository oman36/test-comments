<?php
/**
 * Created by PhpStorm.
 * User: neoman
 * Date: 30.04.17
 * Time: 12:14
 */

namespace Models;
use Helpers\DB;

/**
 * Class Model
 * @package Models
 *
 */
class Model
{
    protected $table = null;

    function __construct($data = null)
    {
        if (!is_array($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param int $id
     * @return self
     */
    public static function find($id)
    {
        $query = DB::getConnection()->prepare("SELECT * FROM `" .
            self::_getTable() ."` WHERE `id`=:id");
        $query->bindValue("id",$id);
        if ($query->execute()) {
            return $query->fetchObject(self::class);
        } else {
            throw new \PDOException($query->errorInfo()[2]);
        }
    }

    /**
     * @return string
     */
    public static function generateTableName()
    {
        $className = get_called_class();
        $className = preg_replace('~^Models\\\~','',$className);
        $className = preg_replace("~([A-Z])~","_$1",$className);
        $className = mb_convert_case($className,MB_CASE_LOWER);
        $className = mb_substr($className,1);

        return $className;
    }

    /**
     * @param array $data
     * @return false|Model
     */
    public static function insert($data)
    {
        $class = get_called_class();
        $model = new $class($data);
        /** @var Model $model */
        $id = DB::insert($model->getTable(),$data);
        if (false === $id) {
            return $id;
        } else {
            $model->id = $id;
            return $model;
        }
    }

    /**
     * @return DB
     */
    public static function query()
    {
        return DB::query()->from(self::_getTable());
    }

    private static function _getTable()
    {
        $class = get_called_class();
        return (new $class)->getTable();
    }

    /**
     * @return string
     */
    public function getTable()
    {
        if ($this->table) {
            return $this->table;
        }

        return $this->table = self::generateTableName();
    }
}