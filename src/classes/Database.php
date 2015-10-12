<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy;


use DbEasy\Adapter\AdapterAbstract;
use DbEasy\Placeholder\Common;
use DbEasy\Placeholder\Float;
use DbEasy\Placeholder\Identifier;
use DbEasy\Placeholder\PlaceholderInterface;
use DbEasy\Placeholder\Prefix;
use DbEasy\Placeholder\Reference;
use DbEasy\Placeholder\ValuesList;
use DbEasy\Placeholder\WholeNumber;

class Database
{

    /**
     * @var AdapterAbstract
     */
    private $adapter = null;


    /**
     * @var \Closure
     */
    private $errorHandler = null;


    /**
     * @var DSN
     */
    private $dsn = null;

    /**
     * @var PlaceholderInterface[]
     */
    private $placeholders = array();


    /**
     * @var string
     */
    private $identPrefix;

    /**
     * constructor disabled
     */
    private function __construct(DSN $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @param string $dsn
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
        $params = array_slice((is_array($sql)) ? $sql : func_get_args(), 1);
        $query = (is_array($sql)) ? $sql[0] : $sql;

        $adapter = $this->getAdapter();

        $query = $this->transformQuery($query, $params);
        $result = $adapter->execute($query);

        $error = $this->adapter->getLastError();
        if (!empty($error)) {
            call_user_func_array($this->errorHandler, ["message", $error]);
        }

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

        return $this->transformQuery($query, $params, true);
    }

    public function setIdentPrefix($prefix)
    {
        $this->identPrefix = $prefix;
    }


    public function setAdapter(AdapterAbstract $adapter)
    {
        $this->adapter = $adapter;
        $this->adapter->setDsn($this->dsn);
    }

    private function getAdapter(){
        if(!is_null($this->adapter)){
            return $this->adapter;
        }

        $engineClassName = "DbEasy\\Adapter\\" . ucfirst($this->dsn->getScheme());

        if(class_exists($engineClassName)){
            $this->adapter = new $engineClassName();
            $this->adapter->setDsn($this->dsn);
        }

        return null;
    }

    public function setPlaceholder(PlaceholderInterface $placeholder)
    {
        $this->placeholders[$placeholder->getName()] = $placeholder;
    }

    private function getPlaceholder($name)
    {
        if(isset($this->placeholders[$name]))
            return $this->placeholders[$name];

        return null;
    }


    private function getPlaceholdersRegexpString(){
        $regexp = "";
        /** @var PlaceholderInterface $placeholder */
        foreach ($this->placeholders as $placeholder) {
            $regexp .= $placeholder->getRegexp();
        }
        return $regexp;
    }


    /**
     * @param $handler
     */
    public function setErrorHandler($handler)
    {
        $this->errorHandler = $handler;
    }



    /**
     * @param Query $query
     * @param bool $expandValues
     * @return Query
     */
    public function transformQuery($query, $params, $expandValues = false)
    {
        $re = '{
            (?>
                # Ignored chunks.
                (?>
                    # Comment.
                    -- [^\r\n]*
                )
                  |
                (?>
                    # DB-specifics.
                    ' . trim($this->adapter->getRegexpForIgnorePlaceholder()) . '
                )
            )
              |
            (?>
                # Optional blocks
                \{
                    # Use "+" here, not "*"! Else nested blocks are not processed well.
                    ( (?> (?>[^{}]+)  |  (?R) )* )             #1
                \}
            )
              |
            (?>
                # Placeholder
                (\?) ( ['. $this->getPlaceholdersRegexpString() .']? )                           #2 #3
            )
        }sx';

        $values = [];
        $transformQueryAsText = preg_replace_callback(
            $re,
            function ($matches) use ($expandValues, &$values) {
                if (!empty($matches[2])) {
                    $placeholder = $this->getPlaceholder($matches[3]);
                }
            },
            $query
        );

        return Query::create($transformQueryAsText, $values);
    }


    private function initDefaultPlaceholders()
    {
        $this->setPlaceholder(new Common());
        $this->setPlaceholder(new Float());
        $this->setPlaceholder(new Identifier());
        $this->setPlaceholder(new Prefix());
        $this->setPlaceholder(new Reference());
        $this->setPlaceholder(new ValuesList());
        $this->setPlaceholder(new WholeNumber());
    }

}