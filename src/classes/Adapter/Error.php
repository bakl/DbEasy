<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 22.10.15
 */

namespace DbEasy\Adapter;


class Error
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * Error constructor.
     * @param string $code
     * @param string $message
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}