<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 16.10.15
 */

namespace DbEasy;


use DbEasy\Adapter\AdapterAbstract;
use DbEasy\Placeholder\PlaceholderCollection;

class QueryTransformer
{
    /**
     * @var bool
     */
    private $isForceExpandValues = false;

    /**
     * @var AdapterAbstract
     */
    private $adapter;

    /**
     * @var PlaceholderCollection
     */
    private $placeholders;

    /**
     * @var int
     */
    private $numberPlaceholder = 0;

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var array
     */
    private $preparedValues = [];

    /**
     * @var bool
     */
    private $isHookSkipValue = false;

    /**
     *  constructor
     * @param AdapterAbstract $adapter
     * @param PlaceholderCollection $placeholders
     */
    public function __construct(AdapterAbstract $adapter, PlaceholderCollection $placeholders)
    {
        $this->adapter = $adapter;
        $this->placeholders = $placeholders;
    }

    /**
     * @return string
     */
    public function getRegexpMain()
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
                (\?) ( [' . $this->placeholders->getAllPlaceholdersAsString() . ']? )                           #2 #3
            )
        }sx';
        return $re;
    }

    /**
     * @param Query $query
     * @param bool $isForceExpandValues
     * @return Query
     */
    public function transformQuery(Query $query, $isForceExpandValues = false)
    {
        $this->isForceExpandValues = $isForceExpandValues;
        $this->values = $query->getValues();
        $this->preparedValues = [];
        $this->numberPlaceholder = 0;
        $this->isHookSkipValue = false;

        $transformQueryAsText = preg_replace_callback(
            $this->getRegexpMain(),
            [$this, 'transformCallback'],
            $query->getQueryAsText()
        );

        return Query::create($transformQueryAsText, $this->preparedValues);
    }

    /**
     * @param $matches
     * @return mixed
     */
    private function transformCallback($matches)
    {
        $replacement = $matches[0];

        if (!empty($matches[1])) {
            $tmpPreparedValues = $this->preparedValues;
            $tmpNumberPlaceholder = $this->numberPlaceholder;
            $tmpIsHookSkipValue = $this->isHookSkipValue;
            $replacement = preg_replace_callback(
                $this->getRegexpMain(),
                [$this, 'transformCallback'],
                $matches[1]
            );

            if ($this->isHookSkipValue) {
                $this->isHookSkipValue = $tmpIsHookSkipValue;
                $this->numberPlaceholder = $tmpNumberPlaceholder;
                $this->preparedValues = $tmpPreparedValues;
                return '';
            }

            return $replacement;
        }

        if (!empty($matches[2])) {
            $this->numberPlaceholder++;
            $placeholder = $this->placeholders->getPlaceholder($matches[0]);

            if (count($this->values) == 0) {
                return 'ERROR_NO_VALUE';
            }

            $value = array_shift($this->values);

            if ($value === Database::SKIP_VALUE) {
                $this->isHookSkipValue = true;
            }

            if ($this->isForceExpandValues) {
                $replacement = $placeholder->transformPlaceholder($value);
            } else {
                $replacement = $placeholder->transformPlaceholder($value, $this->adapter->getNativeCommonPlaceholder($this->numberPlaceholder));

                $preparedValue = $placeholder->transformValue($value);
                if ($preparedValue !== Database::SKIP_VALUE) {
                    if (is_array($preparedValue)) {
                        $this->preparedValues = array_merge($this->preparedValues, $preparedValue);
                    } else {
                        $this->preparedValues = array_merge($this->preparedValues, [$preparedValue]);
                    }
                }
            }
        }

        return $replacement;
    }
}