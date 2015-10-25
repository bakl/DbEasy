<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy\Adapter;

use DbEasy\Query;

class Sqlite extends AdapterAbstract
{
    /**
     * @return mixed
     */
    public function connect()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->registerNewError('-1', 'PDO sqlite extension is not loaded');
            return false;
        }

        if ($this->dsn->getPath() === ':memory:') {
            $this->connection = new \PDO('sqlite::memory:');
        } else {
            $this->connection = new \PDO('sqlite:' . $this->dsn->getPath());
        }

        $errorInfo = $this->connection->errorInfo();
        if (!is_null($this->connection->errorCode())) {
            $this->registerNewError($errorInfo[1], $errorInfo[2]);
            return false;
        }

        return true;
    }

    /**
     * @param Query $query
     * @return mixed
     */
    protected function executeQuery(Query $query)
    {
        /** @var \PDOStatement $stmt */
        $stmt = $this->connection->prepare($query->getQueryAsText());

        if ($stmt === false) {
            $errorInfo = $this->connection->errorInfo();
            $this->registerNewError($errorInfo[1], $errorInfo[2]);
            return false;
        }

        if (!$stmt->execute($query->getValues())) {
            $errorInfo = $stmt->errorInfo();
            $this->registerNewError($errorInfo[1], $errorInfo[2]);
            return false;
        }

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result === false) {
            $errorInfo = $stmt->errorInfo();
            $this->registerNewError($errorInfo[1], $errorInfo[2]);
            return false;
        }

        return $result;
    }

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