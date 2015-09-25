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
            "/\?a|\?/",
            function($matches) use (&$params, &$unusedParams){
                $placeHolder = $matches[0];
                switch($placeHolder){
                    case "?a":
                        $arrayParams = array_pop($params);
                        array_walk($arrayParams, function(&$item){
                            $item = $this->escapeParam($item);
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

    protected function escapeOnDbLayer($param){
        return $param;
    }


}