<?php

namespace Cloudstash\Point\Routing;

use Cloudstash\Point\Helper\Arr;
use Cloudstash\Point\Helper\Str;
use Cloudstash\Point\HTTP\Uri;

class Route
{
    protected $available_method = [];

    protected $pattern_strict = [];

    protected $values = [];

    /**
     * @return string
     */
    protected function getCurrentUri()
    {
        $requestPath = (new Uri())->getRequestPath();
        return Str::RemoveFirst('/', $requestPath);
    }

    public function __construct($method = ['GET', 'POST'])
    {
        if (!is_array($method)) {
            $method = (array) $method;
        }

        $this->available_method = $method;
    }

    public function registerBlock($name, $matcher = null)
    {
        $this->pattern_strict[$name] = $matcher;

        return $this;
    }

    public function getValues()
    {
        return (array) $this->values;
    }

    public function isCurrent()
    {
        $uriArray = explode('/', $this->getCurrentUri());

        $index = -1;

        foreach ($this->pattern_strict as $name => $matcher) {
            $index++;
            $partial = Arr::get($uriArray, $index);

            if (is_null($partial)) {
                return false;
            }

            if ($partial == $matcher) {
                $this->values[$name] = $partial;

                continue;
            }

            if (is_callable($matcher)) {
                if (call_user_func_array($matcher, [$partial])) {
                    $this->values[$name] = $partial;
                    continue;
                }
            }

            return false;
        }

        return true;
    }
}