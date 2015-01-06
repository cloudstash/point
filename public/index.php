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

$ins = new \Doctrine\Instantiator\Instantiator();

$home_controller = $ins->instantiate(Controller\Home::class);

print_r(Cloudstash\Point\Helper\Arr::toAssocOneToOne($test, 'id', 'name'));

print $home_controller->actionDefault();