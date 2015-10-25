<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;



class ValuesList extends PlaceholderAbstract
{
    /**
     * constructor is required for initialization placeholder name
     */
    public function __construct()
    {
        $this->setName('?a');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function transformValue($value)
    {
        return array_values($value);
    }

    /**
     * @param $value
     * @param string $nativePlaceholder
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '')
    {
        $resultValues = array();
        foreach ($value as $valueRowKey => $valueRowData) {
            $valueRowData = $this->getQuotePerformer()->quote($valueRowData);

            if(!is_int($valueRowKey)){
                $resultValues[] = implode(
                    ' = ',
                    [
                        $this->getQuotePerformer()->quoteIdentifier($valueRowKey),
                        (empty($nativePlaceholder)) ? $valueRowData : $nativePlaceholder
                    ]
                );
            } else {
                $resultValues[] = (empty($nativePlaceholder)) ? $valueRowData : $nativePlaceholder;
            }
        }

        return implode(',', $resultValues);
    }
}