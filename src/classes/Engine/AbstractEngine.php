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
                        return implode(',', array_pop($params));
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

    protected escape()


}