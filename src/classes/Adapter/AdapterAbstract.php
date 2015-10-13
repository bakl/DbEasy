<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy\Adapter;


use DbEasy\DSN;
use DbEasy\Query;

abstract class AdapterAbstract
{
    /**
     * @var DSN
     */
    protected $dsn;

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * @var Error
     */
    protected $error;

    /**
     * @return string
     */
    abstract public function getRegexpForIgnorePlaceholder();

    /**
     * @return mixed
     */
    abstract protected function connect();

    /**
     * @param Query $query
     * @return array
     */
    abstract protected function executeQuery(Query $query);

    /**
     * AdapterAbstract constructor.
     */
    final public function __construct()
    {
    }

    /**
     * @return DSN
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param DSN $dsn
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }



    /**
     * @param Query $query
     * @return array
     */
    final public function execute(Query $query)
    {
        if (empty($this->connection)) {
            $this->connect();
        }

        $result = $this->executeQuery($query);
        return $result;
    }

    /**
     * @return string
     */
    public function getNativePlaceholder($n)
    {
        return '?';
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->error;
    }

    public function setLastError($code, $message)
    {

    }

}