<?php
/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:57
 */

namespace DbEasy\Engine;


class AbstractEngine
{
    protected function expandPlaceHolders($query, $params, $expandNative = false){
        $params = array_reverse($params);
        $unusedParams = array();

        $query = preg_replace_callback(
            "/\?#|\?f|\?d|\?a|\?/",
            function($matches) use (&$params, &$unusedParams){
                $placeHolder = $matches[0];
                switch($placeHolder){
                    case "?#":
                        return $this->escapeIdentifier(array_pop($params));
                        break;
                    case "?f":
                        $floatParam = array_pop($params);
                        if(!is_float($floatParam))
                            throw new \Exception("Not float value passed for ?f placeholder " . $floatParam);
                        return $this->escapeParam($floatParam);
                        break;
                    case "?d":
                        $intParam = array_pop($params);
                        if(!is_int($intParam))
                            throw new \Exception("Not int value passed for ?d placeholder" . $intParam);
                        return $this->escapeParam($intParam);
                        break;
                    case "?a":
                        $arrayParams = array_pop($params);
                        array_walk($arrayParams, function(&$item, &$key){
                            $item = $this->escapeParam($item);
                            if(!is_int($key)) {
                                $key = $this->escapeIdentifier($key);
                                $item = implode("=", array($key, $item));
                            }
                        });
                        return implode(',', $arrayParams);
                        break;
                    case "?":
                        array_push($unusedParams, array_pop($params));
                        return $placeHolder;
                        break;
                    default:
                        return $placeHolder;
                }
            },
            $query
        );


        return array($query, $unusedParams);
    }

    protected function escapeParam($param){
        if(is_int($param))
            return $param;
        if(is_float($param))
            return str_replace(',', '.', $param);

        return $this->escapeOnDbLayer($param);
    }

    protected function escapeIdentifier($param){
        //@TODO may be this wrong way?!
        return str_replace("'", '`', $this->escapeOnDbLayer($param));
    }

    protected function escapeOnDbLayer($param){
        return $param;
    }


}