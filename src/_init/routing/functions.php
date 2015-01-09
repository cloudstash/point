<?php

if (!function_exists('register_route_filter')) {
    /**
     * @param string $name
     * @param callable $callback
     * @throws Exception
     */
    function register_route_filter($name, $callback) {
        \Cloudstash\Point\Routing\Dispatcher::registerFilter($name, $callback);
    }
}

if (!function_exists('route')) {
    /**
     * @param string $name
     * @param string $pattern
     * @param callable $handler
     * @param array $method
     */
    function route($name, $pattern, $handler, $method = ['GET', 'POST']) {
        \Cloudstash\Point\Routing\Dispatcher::Instance()
            ->register($name, $pattern, $handler, $method);
    }
}

if (!function_exists('route_get')) {
    /**
     * @param string $name
     * @param string $pattern
     * @param callable $handler
     */
    function route_get($name, $pattern, $handler) {
        \Cloudstash\Point\Routing\Dispatcher::Instance()
            ->register($name, $pattern, $handler, ['GET']);
    }
}

if (!function_exists('route_post')) {
    /**
     * @param string $name
     * @param string $pattern
     * @param callable $handler
     */
    function route_post($name, $pattern, $handler) {
        \Cloudstash\Point\Routing\Dispatcher::Instance()
            ->register($name, $pattern, $handler, ['POST']);
    }
}

if (!function_exists('route_not_found')) {
    /**
     * @param callable $handler
     */
    function route_not_found($handler) {
        \Cloudstash\Point\Routing\Dispatcher::Instance()
            ->registerNotFound($handler);
    }
}

if (!function_exists('route_index')) {
    /**
     * @param callable $handler
     */
    function route_index($handler) {
        \Cloudstash\Point\Routing\Dispatcher::Instance()
            ->registerDefault($handler);
    }
}

if (!function_exists('route_to_url')) {
    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    function route_to_url($name, array $params = []) {
        return \Cloudstash\Point\Routing\Dispatcher::Instance()
            ->toUrl($name, $params);
    }
}