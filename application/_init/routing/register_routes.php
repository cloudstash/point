<?php

route_not_found ('TestApp.Controller.Error@404');
route_index ('TestApp.Controller.Home@Default');

route ('simple', '/say_hello/fsrger/ge/ge/g/erg/erg/<a@email>/ger/v/e/<b@int>/gver/<c@ip>/ver/v', function($a, $b, $c) {
    print "Wow! ({$a}) ({$b}) ({$c})!";
});

route_get ('test', '/this_it/<name>', function($name) {
    print $name;
});

route_get ('homeme', '/homeme/<id@int>', 'TestApp.Controller.Home@HomeMe');

route ('default', '/<controller>/<action>', 'TestApp.Controller.#{controller}@#{action}');