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
        $dsnHelper = new DSNHelper();
        $parsedDSN = $dsnHelper->parse($dsn);

        if(is_null($parsedDSN))
            throw new \Exception("Couldn't parse DSN: " . $dsn);

        $this->dbLink = new \PDO(
            sprintf('mysql:dbname=%s;host=', ltrim($parsedDSN['path'],'/'), $parsedDSN['host']),
            $parsedDSN['user'],
            $parsedDSN['pass']
        );

    }

    public function query($query, $params)
    {
        //Befory query expand non-native placeholders
        list($query, $params) = $this->expandPlaceHolders($query, $params);

        var_dump($query, $params);

        $stmt = $this->dbLink->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}