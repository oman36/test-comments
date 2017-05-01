<?php
/**
 * Created by PhpStorm.
 * User: neoman
 * Date: 30.04.17
 * Time: 12:14
 */

namespace Helpers;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class DB
 * @package Models
 *
 * @property \PDO $_connection
 */
class DB
{
    /**
     * @var \PDO $_connection
     */
    private static $_connection;

    private $_type = "select";
    private $_select = "*";
    private $_from = [];
    private $_where = [];
    private $_whereOr = [];
    private $_prepared = [];
    private $_orderBy = [];
    private $_limit = "";

    static function connect($database, $host = "localhost", $port = 3306, $user = "root", $pass = "root")
    {
        $dsn = "mysql:dbname={$database}" .
            ";host={$host}" .
            ";port={$port}" .
            ";charset=UTF8";

        self::$_connection = new \PDO($dsn,$user,$pass);
    }

    /**
     * @return \PDO
     */
    static function getConnection()
    {
        return self::$_connection;
    }

    /**
     * @param string $table
     * @param array $data
     * @return string | false
     */
    public static function insert($table,$data)
    {
        $sql = "INSERT INTO `{$table}`";

        $keys = array_keys($data);
        $sql .= " (`" . implode("`,`",$keys) . "`)";
        $sql .= " VALUES (:" . implode(",:",$keys) . ")";

        $query = DB::getConnection()->prepare($sql);

        foreach ($data as $key => $value) {
            $query->bindValue($key,$value);
        }

        if ($query->execute()) {
            return DB::getConnection()->lastInsertId();
        } else {
            throw new \PDOException($query->errorInfo()[2]);
        }
    }

    /**
     * @param string $table
     *
     * @return  $this
     */
    public function from($table)
    {
        if (is_array($table)) {
            foreach ($table as $one) {
                $this->_from[] = $one;
            }
        } else {
            $this->_from[] = $table;
        }
        return $this;
    }

    /**
     * @param string $column
     *
     * @return  $this
     */
    public function select($column)
    {
        if ("*" === $this->_select) {
            $this->_select = [];
        }
        if (is_array($column)) {
            foreach ($column as $one) {
                $this->_select[] = $one;
            }
        } else {
            $this->_select[] = $column;
        }
        return $this;
    }

    /**
     * @param string $col
     * @param string $operator
     * @param string $value
     *
     * @return  $this
     */
    public function where($col,$operator,$value)
    {
        $this->_where[] = [$col,$operator,$value];
        return $this;
    }

    /**
     * @param string $col
     * @param string $operator
     * @param string $value
     *
     * @return  $this
     */
    public function whereOr($col,$operator,$value)
    {
        $this->_whereOr[] = [$col,$operator,$value];
        return $this;
    }

    /**
     * @param array $cols
     * @return array | false
     */
    public function get($cols = null)
    {
        if (null !== $cols) {
            if (!is_array($cols)) {
                throw new Exception("Need array, but " . gettype($cols) . " given");
            }
            $this->_select = $cols;
        }

        $this->_type = "select";
        $sql = $this->getSql();
        $query = self::$_connection->prepare($sql);
        foreach ($this->_prepared as $name => $value) {
            $query->bindValue($name,$value);
        }

        if ($query->execute()) {
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            throw new \PDOException($query->errorInfo()[2]);
        }

    }

    public function getSql()
    {
        switch ($this->_type) {
            case "select" :
                $select = is_array($this->_select) ?
                    implode(",", $this->_select) : $this->_select;
                $from = implode(",", $this->_from);

                $sql = "SELECT {$select} FROM {$from}";
                break;
            default : return false;
        }

        if ($this->_where || $this->_whereOr) {
            $sql .= " WHERE ";
        }
        if ($this->_where) {
            $conditions = $this->prepareWhere($this->_where);
            $sql .= implode(" AND ", $conditions);
        }
        if ($this->_whereOr) {
            $conditions = $this->prepareWhere($this->_where);
            $sql .= implode(" OR ", $conditions);
        }

        if ($this->_orderBy) {
            $sql .= " ORDER BY ";
            $orders = [];
            foreach ($this->_orderBy as $field => $direction) {
                $orders[] = $field . " " . $direction;
            }
            $sql .= implode(",",$orders);
        }
        
        if(!empty($this->_limit)) {
            $sql .= " LIMIT {$this->_limit}";
        }
        return $sql;
    }

    public function prepareWhere($array)
    {
        $conditions = [];
        foreach ($array as $condition) {
            if (preg_match('~(not\s+in|in)~i',$condition[1])) {
                $condition[2] = "('" . implode("','",$condition[2]) ."')";
            }
            $this->_prepared[$condition[0]] = $condition[2];
            $condition[2] = ":" . $condition[0];
            $conditions[] = implode(" ",$condition);
        }
        return $conditions;
    }

    public static function query()
    {
        return new self();
    }

    /**
     * @param array | string $field
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($field, $direction = "ASC")
    {
        if (!is_array($field)) {
            $field = [$field => $direction];
        }

        foreach ($field as $name => $direction) {
            $this->_orderBy[$name] = $direction;
        }

        return $this;
    }

    /**
     * @param string $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }
}