<?php

require_once "../application/bootstrap.php";

$test = [
    [
        'id' => 123,
        'name' => 'Follow me'
    ],
    [
        'id' => 23,
        'name' => 'Foefllow me'
    ]
];

print_r(Cloudstash\Point\Helper\Arr::toAssocOneToOne($test, 'id', 'name'));

print (new Controller\Home())->actionDefault();