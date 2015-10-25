<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy\Adapter;


use DbEasy\DSN;
use DbEasy\Query;
use DbEasy\QuotePerformerInterface;

abstract class AdapterAbstract implements QuotePerformerInterface
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
     * @var Error[]
     */
    protected $errors;

    /**
     * @return string
     */
    abstract public function getRegexpForIgnorePlaceholder();

    /**
     * @return bool
     */
    abstract public function connect();

    /**
     * @return int
     */
    abstract public function getRowsCountAffectedInLastQuery();

    /**
     * @return int
     */
    abstract public function getLastInsertId();

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
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
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
     * null if db without native placeholders
     * @return string
     */
    public function getNativeCommonPlaceholder($n)
    {
        return '?';
    }

    /**
     * @param string $code
     * @param string $message
     */
    public function registerNewError($code, $message) {
        $this->errors[] = new Error($code, $message);
    }

    /**
     * @return Error[]
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return Error
     */
    public function getLastError()
    {
        if (empty($this->errors[count($this->errors) - 1])) {
            return false;
        }

        return $this->errors[count($this->errors) - 1];
    }
}