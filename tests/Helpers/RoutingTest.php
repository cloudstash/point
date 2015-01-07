<?php

class Test_Helpers_Routing extends \TestCase
{
    public function testCleanUrl()
    {
        $url = '      /controller/action////id/1////';
        $right = 'controller/action/id/1';
        $result = \Cloudstash\Point\Helper\Routing::cleanUrl($url);

        $this->assertEquals($result, $right, 'Wrong URL cleanup');
    }

    public function testExplodeUrl()
    {
        $url = '      /controller/action////id/1////';
        $right = ['controller', 'action', 'id', '1'];
        $result = \Cloudstash\Point\Helper\Routing::explodeUrl($url);
        $result = \Cloudstash\Point\Helper\Arr::similar($right, $result, false);

        $this->assertTrue($result, 'Wrong URL explode');

        $url = '     /     ';
        $right = [];
        $result = \Cloudstash\Point\Helper\Routing::explodeUrl($url);
        $result = \Cloudstash\Point\Helper\Arr::similar($right, $result, false);

        $this->assertTrue($result, 'Wrong empty URL explode');
    }
}