<?php
/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:29
 */
namespace DbEasy\Engine;


/**
 * NOT USE IT!!!!
 *
 * Interface EngineInterface
 * @package DbEasy\Engine
 * @deprecated
 */
interface EngineInterface
{
    public function connect($dsn);

    public function query($query, $params);

    public function setIdentPrefix($prefix);

    public function getQuery($query, $params);

    public function initialized();
}