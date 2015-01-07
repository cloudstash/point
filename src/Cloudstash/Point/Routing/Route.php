<?php

namespace Cloudstash\Point\Routing;

use Cloudstash\Point\Helper\Arr;
use Cloudstash\Point\Helper\Routing;

class Route
{
    const TYPE_BLOCK = 'block';
    const TYPE_VARIABLE = 'variable';

    protected $pattern_strict = [];
    protected $values = [];
    protected $handler = null;

    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return callable
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $name
     * @param mixed|callback $matcher
     * @return $this
     */
    public function registerVar($name, $matcher)
    {
        $this->pattern_strict[] = [
            'type' => self::TYPE_VARIABLE,
            'name' => $name,
            'matcher' => $matcher
        ];

        return $this;
    }

    /**
     * @param mixed|callback $matcher
     * @return $this
     */
    public function registerBlock($matcher)
    {
        $this->pattern_strict[] = [
            'type' => self::TYPE_BLOCK,
            'matcher' => $matcher
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return (array) $this->values;
    }

    public function isCurrent($url)
    {
        $uriArray = Routing::explodeUrl($url);

        foreach ($this->pattern_strict as $index => $segment) {
            $partial = Arr::get($uriArray, $index, null);

            if (is_null($partial)) {
                return false;
            }

            $type = Arr::get($segment, 'type', self::TYPE_BLOCK);
            $matcher = Arr::get($segment, 'matcher', null);
            $name = Arr::get($segment, 'name', "var{$index}");

            if (is_null($matcher)) {
                return false;
            }

            if ($partial == $matcher) {
                if ($type == self::TYPE_VARIABLE) {
                    $this->values[$name] = $partial;
                }

                continue;
            }

            if (is_callable($matcher)) {
                if (call_user_func_array($matcher, [$partial])) {
                    if ($type == self::TYPE_VARIABLE) {
                        $this->values[$name] = $partial;
                    }

                    continue;
                }
            }

            return false;
        }

        return true;
    }
}