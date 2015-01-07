<?php

namespace Controller;

use Cloudstash\Point\HTTP\HttpRequest;

class Home
{
    public function actionDefault()
    {
        var_dump(HttpRequest::Instance()->getVariables());
    }
}