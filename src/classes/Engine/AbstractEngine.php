<?php
/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:57
 */

namespace DbEasy\Engine;


class AbstractEngine
{
    protected $identPrefix = "";

    public function setIdentPrefix($prefix){
        $this->identPrefix = $prefix;
    }

    protected function expandPlaceHolders($query, $params, $expandNative = false){
        $params = array_reverse($params);
        $unusedParams = array();

        $query = preg_replace_callback(
            "/(\?(?:#|f|d|a|_)*)+([^\{\}\/,\.\s\(\)]+)*/",
            function($matches) use (&$params, &$unusedParams, $expandNative){
                $placeHolder = $matches[1];
                $placeHolderParam = (isset($matches[2])) ? $matches[2] : "";
                switch($placeHolder){
                    case "?#":
                        return $this->escapeIdentifier(array_pop($params));
                        break;
                    case "?f":
                        $floatParam = floatval(array_pop($params));
                        return $this->escapeParam($floatParam);
                        break;
                    case "?d":
                        $intParam = intval(array_pop($params));
                        return $this->escapeParam($intParam);
                        break;
                    case "?a":
                        $arrayParams = array_pop($params);
                        if($arrayParams == DBSIMPLE_SKIP) return DBSIMPLE_SKIP;
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
                        if($expandNative){
                            return $this->escapeParam(array_pop($params));
                        } else {
                            array_push($unusedParams, array_pop($params));
                            return $placeHolder;
                        }
                        break;
                    case "?_":
                        return $this->escapeIdentifier($this->identPrefix.$placeHolderParam);
                    default:
                        return $placeHolder;
                }
            },
            $query
        );

        $query = preg_replace_callback(
            "/\{(\s|.*?)\}/si",
            function($matches)  use (&$unusedParams) {
//                var_dump($matches);
                if(preg_match("/".DBSIMPLE_SKIP."/", $matches[1]))
                    return "";
                if(preg_match_all("/\?/", $matches[1], $matchesPlaceholders)) {
                    $found = false;
                    for ($i = 0; $i < count($matchesPlaceholders[0]); $i++) {
                        if ($unusedParams[$i] == DBSIMPLE_SKIP || $found) {
                            unset($unusedParams[$i]);
                            $found = true;
                        }
                    }
                    $unusedParams = array_values($unusedParams);
                    if ($found) return "";
                }
                return $matches[1];
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