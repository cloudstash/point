<?php

namespace Cloudstash\Point\HTTP;

use Cloudstash\Helper\Arr;

class Server
{
    protected static $instance = null;

    protected $server = null;

    /**
     * @return Server
     */
    public static function Instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __clone() {}

    protected function __construct()
    {
        $this->server = $_SERVER;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return Arr::get($this->server, 'REQUEST_METHOD', 'GET');
    }
}