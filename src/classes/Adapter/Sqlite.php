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
    protected function connect()
    {
        if ($this->dsn->getHost() === 'memory') {
            $this->connection = new \PDO('sqlite::memory:');
        } else {
            $this->connection = new \PDO('sqlite::' . $this->dsn->getPath());
        }
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

        if ($stmt->execute($query->getValues())) {
            return false;
        }

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result === false) {
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
     * @param mixed $value
     * @return mixed
     */
    public function escape($value)
    {
        // TODO: Implement escape() method.
    }
}