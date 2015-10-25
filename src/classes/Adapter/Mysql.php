<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy\Adapter;

use DbEasy\Query;

class Mysql extends AdapterAbstract
{
    /**
     * @return mixed
     */
    public function connect()
    {
        $this->connection = new \PDO(
            sprintf(
                'mysql:dbname=%s;host=%s',
                ltrim($this->dsn->getPath(),'/'),
                $this->dsn->getHost()
            ),
            $this->dsn->getUser(),
            $this->dsn->getPassword()
        );
    }

    /**
     * @param Query $query
     * @return mixed
     */
    protected function executeQuery(Query $query)
    {

        $stmt = $this->connection->prepare($query->getQueryAsText());
//        var_dump($query, $params);
        if(!$stmt->execute($query->getValues())){
//            echo my_backtrace();
            var_dump($stmt->errorInfo(), $query);exit;
        };

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(empty($result)){
            if(preg_match('/^\s* INSERT \s+/six', $query))
                $result = $this->connection->lastInsertId();
            if(preg_match('/^\s* DELETE|UPDATE \s+/six', $query))
                $result = $stmt->rowCount();
        }

        return $result;
    }

    public function getRegexpForIgnorePlaceholder()
    {
        return '
            "   (?> [^"\\\\]+|\\\\"|\\\\)*    "   |
            \'  (?> [^\'\\\\]+|\\\\\'|\\\\)* \'   |
            `   (?> [^`]+ | ``)*              `   |   # backticks
            /\* .*?                          \*/      # comments
        ';
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function quote($value)
    {
        return $this->connection->quote($value);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function quoteIdentifier($value)
    {
        // TODO: Implement quoteIdentifier() method.
    }

    /**
     * @return int
     */
    public function getRowsCountAffectedInLastQuery()
    {
        // TODO: Implement getRowsCountAffectedInLastQuery() method.
    }

    /**
     * @return int
     */
    public function getLastInsertId()
    {
        // TODO: Implement getLastInsertId() method.
    }
}