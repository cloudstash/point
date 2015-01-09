<?php

namespace TestApp\Controller;

use Cloudstash\Point\HTTP\HttpRequest;

class Home
{
    public function actionDefault()
    {
        print "Welcome to us";
    }

    public function actionHomeMe()
    {
        print "HomeMe # " . HttpRequest::Instance()->getVariable('id', 0);
    }
}