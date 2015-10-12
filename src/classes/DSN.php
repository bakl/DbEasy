<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 11.09.15
 */

namespace DbEasy;


class DSN
{
    /**
     * example: mysql
     * @var string
     */
    private $scheme = '';

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var int
     */
    private $port = 0;

    /**
     * @var string
     */
    private $user = '';

    /**
     * @var string;
     */
    private $password = '';

    /**
     * @var string;
     */
    private $path = '';

    /**
     * after sign ?
     * @var string
     */
    private $query = '';

    /**
     * after sign #
     * @var string
     */
    private $fragment = '';

    /**
     * @param $dsn
     * @throws DatabaseException
     */
    public function __construct($dsn)
    {
        if (empty($dsn)) {
            throw new DatabaseException('Empty dsn');
        }

        if (!is_array($dsn)) {
            $dsn = parse_url($dsn);
        }

        if (!empty($dsn['scheme'])) {
            $this->setScheme($dsn['scheme']);
        }
        if (!empty($dsn['host'])) {
            $this->setHost($dsn['host']);
        }
        if (!empty($dsn['port'])) {
            $this->setPort($dsn['port']);
        }
        if (!empty($dsn['user'])) {
            $this->setUser($dsn['user']);
        }
        if (!empty($dsn['pass'])) {
            $this->setPassword($dsn['pass']);
        }
        if (!empty($dsn['path'])) {
            $this->setPath($dsn['path']);
        }
        if (!empty($dsn['query'])) {
            $this->setQuery($dsn['query']);
        }
        if (!empty($dsn['fragment'])) {
            $this->setFragment($dsn['fragment']);
        }

    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $fragment
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }

}