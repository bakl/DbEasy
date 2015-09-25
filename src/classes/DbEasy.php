<?php
/**
 * User: sergeymartyanov
 * Date: 24.09.15
 * Time: 23:54
 */

namespace DbEasy;

use DbEasy\Engine\EngineInterface;
use DbEasy\Helpers\DSNHelper;

if (!defined('DBSIMPLE_SKIP'))
    define('DBSIMPLE_SKIP', log(0));

class DbEasy
{
    /** @var EngineInterface  */
    private $engine;

    protected $errorHandler;

    protected $error;

    protected $errmsg;

    protected $logger;

    protected $identPrefix = "";


    public function __construct($dsn)
    {
        $dsnHelper = new DSNHelper();
        $scheme = $dsnHelper->parseScheme($dsn);

        if(is_null($scheme))
            throw new \Exception("Couldn't parse scheme from DSN: " . $dsn);

        $engineClassName = "DbEasy\\Engine\\" . ucfirst($scheme);

        if(!class_exists($engineClassName))
            throw new \Exception("Couldn't find engine for: " . $scheme);

        $this->engine = new $engineClassName();
        $this->engine->connect($dsn);
        $this->engine->setIdentPrefix($this->identPrefix);
    }

    public function query($query)
    {
//        var_dump(is_array($query));
        $params = array_slice((is_array($query)) ? $query : func_get_args(), 1);
        $query = (is_array($query)) ? $query[0] : $query;

//        var_dump("-",$query,$params,"-");
        $result = $this->engine->query($query, $params);
//        var_dump($result);

        return $result;
    }

    public function select()
    {

        $result = call_user_func_array(array($this, 'query') , func_get_args());

        return $result;
    }

    public function selectRow()
    {
        $result = call_user_func_array(array($this, 'query') , func_get_args());

        if(isset($result[0]))
            return $result[0];
        return array();
    }

    public function selectCell()
    {
        $result = call_user_func_array(array($this, 'query') , func_get_args());


        if(isset($result[0]) && count($result[0])) {
            return array_pop($result[0]);
        }
        return "";
    }

    public function selectCol()
    {
        $result = call_user_func_array(array($this, 'query') , func_get_args());

        if(isset($result[0])) {
            $columnName = array_keys($result[0])[0];
            return array_column($result, $columnName);
        }

        return array();
    }

    public function getQuery($query){
        $params = array_slice((is_array($query)) ? $query : func_get_args(), 1);
        $query = (is_array($query)) ? $query[0] : $query;

        return $this->engine->getQuery($query, $params);
    }

    public function setIdentPrefix($prefix)
    {
        $this->identPrefix = $prefix;
    }

    /**
     * callback setErrorHandler(callback $handler)
     * Set new error handler called on database errors.
     * Handler gets 3 arguments:
     * - error message
     * - full error context information (last query etc.)
     */
    public function setErrorHandler($handler)
    {
        $prev = $this->errorHandler;
        $this->errorHandler = $handler;
        // In case of setting first error handler for already existed
        // error - call the handler now (usual after connect()).
        if (!$prev && $this->error && $this->errorHandler) {
            call_user_func($this->errorHandler, $this->errmsg, $this->error);
        }
        return $prev;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    protected function initialized(){
        return (!is_null($this->engine)) ? $this->engine->initialized() : false;
    }
}