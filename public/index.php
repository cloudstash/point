<?php

require_once "../application/bootstrap.php";

// example GET /news/get/123
$route = (new \Cloudstash\Point\Routing\Route('GET'))
    ->registerBlock('controller', 'news')
    ->registerBlock('action', 'get')
    ->registerBlock('id', function($partial) {
        return preg_match('/^(?:\d+)$/', $partial);
    });

if ($route->isCurrent()) {
    var_dump($route->getValues());
} else {
    var_dump($route);
}