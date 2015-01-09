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

    /**
     * @param array $params
     * @return string
     */
    public function toUrl(array $params = [])
    {
        $url = '';

        foreach ($this->pattern_strict as $index => $segment) {
            $type = Arr::get($segment, 'type', self::TYPE_BLOCK);
            $matcher = Arr::get($segment, 'matcher', '');

            if ($type == self::TYPE_BLOCK) {
                $url .= '/' . $matcher;
                continue;
            }

            $name = Arr::get($segment, 'name', "var{$index}");
            $value = Arr::get($params, $name, $name);

            if (is_callable($matcher)) {
                if (!call_user_func_array($matcher, [$value])) {
                    return $url;
                }
            }

            $url .= '/' . $value;
        }

        return $url;
    }

    public function isCurrent($url)
    {
        $uriArray = Routing::explodeUrl($url);

        if (count($this->pattern_strict) != count($uriArray)) {
            return false;
        }

        foreach ($this->pattern_strict as $index => $segment) {
            $partial = Arr::get($uriArray, $index, null);

            if (is_null($partial)) {
                return false;
            }

            $type = Arr::get($segment, 'type', self::TYPE_BLOCK);
            $matcher = Arr::get($segment, 'matcher', null);
            $name = Arr::get($segment, 'name', "var{$index}");

            // block must be strong assert with url partial
            if ($type == self::TYPE_BLOCK) {
                if ($partial == $matcher) {
                    continue;
                }

                return false;
            }

            // if it is variable with callable filter
            if (is_callable($matcher)) {
                if (call_user_func_array($matcher, [$partial])) {
                    $this->values[$name] = $partial;
                    continue;
                }

                return false;
            }

            // if not callable then just set to variable all segment value
            if ($type == self::TYPE_VARIABLE) {
                $this->values[$name] = $partial;
                continue;
            }

            return false;
        }

        return true;
    }
}