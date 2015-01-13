<?php

namespace Cloudstash\Point\Helper;

use Cloudstash\Helper\Str;

class Routing
{
    /**
     * @param string $url
     * @return string
     */
    public static function cleanUrl($url)
    {
        $url = preg_replace('/\\/{2,}/', '/', $url);
        return Str::Trim($url, '/', '/');
    }

    /**
     * @param string $url
     * @return array
     */
    public static function explodeUrl($url)
    {
        $delimiter = '/';

        return Str::explode($delimiter, function() use ($url) {
            return self::cleanUrl($url);
        });
    }
}