<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy\Adapter;

use DbEasy\Adapter\AdapterAbstract;
use DbEasy\Query;

class Sqlite extends AdapterAbstract
{
    /**
     * @return mixed
     */
    public function connect()
    {
        if ($this->dsn->getPath() === ':memory:') {
            $this->connection = new \PDO('sqlite::memory:');
        } else {
            $this->connection = new \PDO('sqlite::' . $this->dsn->getPath());
        }

        // TODO: need use DbSimple method for error hooks
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param Query $query
     * @return mixed
     */
    protected function executeQuery(Query $query)
    {
        $stmt = $this->connection->prepare($query->getQueryAsText());

        if (!$stmt instanceof \PDOStatement) {
            return false;
        }

        if (!$stmt->execute($query->getValues())) {
            return false;
        }

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result === false) {
            print 3;
            return false;
        }

        return $result;

    }

//    /**
//     * @return void
//     */
//    protected function setLastError()
//    {
//        $errorInfo = $this->connection->errorInfo();
//        if (!empty($errorInfo[1])) {
//            $this->error[self::ERROR_CODE] = $errorInfo[0];
//            $this->error[self::ERROR_MESSAGE] = $errorInfo[2];
//        }
//    }

    /**
     * @return string
     */
    public function getRegexpForIgnorePlaceholder()
    {
        return '
            "   (?> [^"\\\\]+|\\\\"|\\\\)*    "   |
            \'  (?> [^\'\\\\]+|\\\\\'|\\\\)* \'   |
            `   (?> [^`]+ | ``)*              `   |   # backticks
            /\* .*?                          \*/      # comments
        /*';
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function quote($value)
    {
        if (empty($this->connection)) {
            $this->connect();
        }

        return $this->connection->quote($value);
    }

    /**
     * TODO: create correct method
     * @param mixed $value
     * @return string
     */
    public function quoteIdentifier($value)
    {
        if (empty($this->connection)) {
            $this->connect();
        }

        return $this->connection->quote($value);
    }
}