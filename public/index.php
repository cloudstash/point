<?php

$time = microtime(true);

register_shutdown_function(function() use ($time) {
    print "<hr>" . (microtime(true) - $time);
});

require_once "../application/bootstrap.php";

\Cloudstash\Point\Routing\Dispatcher::Instance()->handleCurrent();
