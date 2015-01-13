<?php

namespace TestApp\Controller;

class Home
{
    public function actionDefault()
    {
        print "Welcome to us";
    }

    public function actionHomeMe($id)
    {
        print "HomeMe # " . $id;
    }

    public function actionRightNow()
    {
        print "YYYESSS!";
    }
}