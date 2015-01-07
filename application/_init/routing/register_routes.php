<?php

\Cloudstash\Point\Routing\Dispatcher::Instance()
    ->registerDefault(function() {
        print "hello to index";
    })
    ->registerNotFound(function() {
        print "<h1>Not found</h1>";
    })
    ->register('GET, POST', '/<controller:hello>/<hello:name>', function() {
        print "It is worked!";
    })
    ->register('GET', '/test/me/gusta/<id@float>', 'Controller.Home@Default');