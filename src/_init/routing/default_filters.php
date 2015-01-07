<?php

if (!function_exists('register_route_filter')) {
    function register_route_filter($name, $callback) {
        \Cloudstash\Point\Routing\Dispatcher::registerFilter($name, $callback);
    }
}

/**
 * Register default filters
 */

register_route_filter('int', function($partial) {
    return preg_match('/^(?:\d+)$/', $partial);
});

register_route_filter('float', function($partial) {
    return filter_var($partial, FILTER_VALIDATE_FLOAT);
});