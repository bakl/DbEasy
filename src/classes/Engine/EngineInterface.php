<?php
/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:29
 */
namespace DbEasy\Engine;

interface EngineInterface
{
    public function connect($dsn);

    public function query($query, $params);
}