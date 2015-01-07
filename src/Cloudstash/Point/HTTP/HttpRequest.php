<?php

namespace Cloudstash\Point\HTTP;

use Cloudstash\Point\HTTP\Partial\RouteVariables;

class HttpRequest
{
    use RouteVariables;

    protected static $instance = null;

    /**
     * @var Server
     */
    public $server = null;

    protected $get = null;
    protected $post = null;

    /**
     * @return HttpRequest
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
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = Server::Instance();
    }

}