<?php

namespace DbEasy\Engine;
use DbEasy\Helpers\DSNHelper;

/**
 * User: sergeymartyanov
 * Date: 24.09.15
 * Time: 23:54
 */
class Mysql extends AbstractEngine implements EngineInterface
{
    /** @var \PDO  $dbLink */
    private $dbLink;



    public function connect($dsn)
    {
        $parsedDSN = DSNHelper::parse($dsn);

        if(is_null($parsedDSN))
            throw new \Exception("Couldn't parse DSN: " . $dsn);

        $this->dbLink = new \PDO(
            sprintf('mysql:dbname=%s;host=%s', ltrim($parsedDSN['path'],'/'), $parsedDSN['host']),
            $parsedDSN['user'],
            $parsedDSN['pass']
        );

    }

    public function query($query, $params)
    {
        //Befor query expand non-native placeholders
        list($query, $params) = $this->expandPlaceHolders($query, $params);

        $stmt = $this->dbLink->prepare($query);
//        var_dump($query, $params);
        if(!$stmt->execute($params)){
//            echo my_backtrace();
            var_dump($stmt->errorInfo(), $query, $params);exit;
        };

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(empty($result)){
            if(preg_match('/^\s* INSERT \s+/six', $query))
                $result = $this->dbLink->lastInsertId();
            if(preg_match('/^\s* DELETE|UPDATE \s+/six', $query))
                $result = $stmt->rowCount();
        }

        return $result;
    }

    public function getQuery($query, $params)
    {
        list($query, $params) = $this->expandPlaceHolders($query, $params, true);
        return $query;
    }

    protected function escapeOnDbLayer($param){
        return $this->dbLink->quote($param);
    }

    public function initialized(){
        if(!empty($this->dbLink)) return true;
        return false;
    }
}