<?php

$time = microtime(true);

register_shutdown_function(function() use ($time) {
    $xhprof_data = xhprof_disable();

    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, "point2015-debug");
    $xhprof_link = "/xhprof/index.php?run={$run_id}&source=point2015-debug";

    Header ("X-XHProf: {$xhprof_link}");

    print "<hr>" . (microtime(true) - $time)
        . " <a href='{$xhprof_link}' style='font-size: 10px' target='_blank'>[view_profiler]</a>";
});

xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

require_once "../application/bootstrap.php";

\Cloudstash\Point\Routing\Dispatcher::Instance()->handleCurrent();
