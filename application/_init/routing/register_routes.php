<?php

\Cloudstash\Point\Routing\Dispatcher::Instance()
    ->registerNotFound('TestApp.Controller.Error@404')
    ->registerDefault('TestApp.Controller.Home@Default')
    ->register('GET, POST', '/homeme/<id@int>', 'TestApp.Controller.Home@HomeMe');