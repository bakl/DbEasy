<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy;


use DbEasy\Adapter\AdapterAbstract;
use DbEasy\Placeholder\PlaceholderCollection;
use DbEasy\Placeholder\PlaceholderInterface;

// for compatibility with dbsimple:
if (!defined('DBSIMPLE_SKIP')) {
    // Use this constant as placeholder value to skip optional SQL block [...].
    define('DBSIMPLE_SKIP', log(0));
}

if (!defined('DBSIMPLE_ARRAY_KEY')) {
    // hash-based resultset support
    define('DBSIMPLE_ARRAY_KEY', 'ARRAY_KEY');
}

if (!defined('DBSIMPLE_PARENT_KEY')) {
    // forrest-based resultset support
    define('DBSIMPLE_PARENT_KEY', 'PARENT_KEY');
}

class Database
{
    /**
     * constants
     */
    const SKIP = DBSIMPLE_SKIP;
    const ARRAY_KEY = DBSIMPLE_ARRAY_KEY;
    const PARENT_KEY = DBSIMPLE_PARENT_KEY;

    /**
     * @var AdapterAbstract
     */
    private $adapter = null;

    /**
     * @var PlaceholderCollection
     */
    private $placeholders;

    /**
     * @var \Closure
     */
    private $errorHandler = null;

    /**
     * @var DSN
     */
    private $dsn = null;

    /**
     * TODO: move to placeholder
     * @var string
     */
    private $identPrefix;

    /**
     * constructor disabled
     * @param DSN $dsn
     */
    public function __construct(DSN $dsn)
    {
        $this->dsn = $dsn;
        $this->placeholders = new PlaceholderCollection();
        $this->placeholders->addDefaultPlaceholders();
    }

    /**
     * @param $dsnString
     * @return Database
     * @throws DatabaseException
     */
    public static function connect($dsnString)
    {
        if (PHP_VERSION_ID < 50400) {
            throw new DatabaseException('PHP required version >= 5.4');
        }

        $dsn = new DSN($dsnString);
        $db = new Database($dsn);

        return $db;
    }

    /**
     * @param $sql
     * @param mixed ... parameters for replace placeholders
     * @return array
     */
    public function query($sql)
    {
        $query = Query::createByArray(func_get_args());

        $adapter = $this->getAdapter();

        $transformer = new QueryTransformer($this->adapter, $this->placeholders);
        $query = $transformer->transformQuery($query);
        $result = $adapter->execute($query);

        $error = $this->adapter->getLastError();
        if (!empty($error)) {
            call_user_func_array($this->errorHandler, ["message", $error]);
        }

        return $result;
    }


    public function select()
    {

        $result = call_user_func_array(array($this, 'query'), func_get_args());

        return $result;
    }

    public function selectRow()
    {
        $result = call_user_func_array(array($this, 'query'), func_get_args());

        if (isset($result[0]))
            return $result[0];
        return array();
    }

    public function selectCell()
    {
        $result = call_user_func_array(array($this, 'query'), func_get_args());


        if (isset($result[0]) && count($result[0])) {
            return array_pop($result[0]);
        }
        return "";
    }

    public function selectCol()
    {
        $result = call_user_func_array(array($this, 'query'), func_get_args());

        if (isset($result[0])) {
            $columnName = array_keys($result[0])[0];
            return array_column($result, $columnName);
        }

        return array();
    }

    public function getQuery($sql)
    {
        $query = Query::createByArray(func_get_args());
        $transformer = new QueryTransformer($this->adapter, $this->placeholders);
        return $transformer->transformQuery($query, true);
    }

    public function setIdentPrefix($prefix)
    {
        $this->identPrefix = $prefix;
    }


    /**
     * @param AdapterAbstract $adapter
     */
    public function setAdapter(AdapterAbstract $adapter)
    {
        $this->adapter = $adapter;
        $this->adapter->setDsn($this->dsn);
    }

    /**
     * @return AdapterAbstract
     * @throws \Exception
     */
    public function getAdapter()
    {
        if (!is_null($this->adapter)) {
            return $this->adapter;
        }

        $adapterClassName = "DbEasy\\Adapter\\" . ucfirst($this->dsn->getScheme());

        if (class_exists($adapterClassName)) {
            $this->adapter = new $adapterClassName();
            $this->adapter->setDsn($this->dsn);
            return $this->adapter;
        }

        throw new \Exception('Not found adapter for database: ' . $this->dsn->getScheme());
    }

    /**
     * @param PlaceholderInterface $placeholder
     * @return void
     */
    public function addCustomPlaceholder(PlaceholderInterface $placeholder)
    {
        $this->placeholders->addPlaceholder($placeholder);
    }

    /**
     * @param $handler
     */
    public function setErrorHandler($handler)
    {
        $this->errorHandler = $handler;
    }


}