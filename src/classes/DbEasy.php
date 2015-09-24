<?php
/**
 * User: sergeymartyanov
 * Date: 24.09.15
 * Time: 23:54
 */

namespace DbEasy;

use DbEasy\Engine\EngineInterface;
use DbEasy\Helpers\DSNHelper;

class DbEasy
{
    /** @var EngineInterface  */
    private $engine;

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
    }

    public function query($query)
    {
        $params = array_slice(func_get_args(), 1);

        return $this->engine->query($query, $params);
    }

    public function select($query)
    {
        $params = array_slice(func_get_args(), 1);

        return $this->engine->query($query, $params);
    }

    public function selectRow($query)
    {
        $params = array_slice(func_get_args(), 1);

        return $this->engine->query($query, $params)[0];
    }

    public function selectCell($query)
    {
        $params = array_slice(func_get_args(), 1);

        return array_pop($this->engine->query($query, $params)[0]);
    }

    public function selectCol($query)
    {
        $params = array_slice(func_get_args(), 1);
        $result = $this->engine->query($query, $params);

        $columnName = array_keys($result[0])[0];

        return array_column($result, $columnName);
    }
}