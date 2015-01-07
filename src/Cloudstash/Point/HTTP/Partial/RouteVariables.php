<?php

namespace Cloudstash\Point\HTTP\Partial;

use Cloudstash\Point\Helper\Arr;

trait RouteVariables
{
    protected $variables = [];

    /**
     * @return array
     */
    public function getVariables()
    {
        return (array) $this->variables;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getVariable($name, $default = null)
    {
        return Arr::get($this->variables, $name, $default);
    }

    /**
     * @param array $variables
     * @return bool
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function unsetVariable($name)
    {
        if (isset($this->variables[$name])) {
            unset($this->variables[$name]);
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function setVariable($name, $value = null)
    {
        return $this->variables[$name] = $value;
    }
}