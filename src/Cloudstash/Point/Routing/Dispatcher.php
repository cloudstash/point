<?php

namespace Cloudstash\Point\Routing;

use Cloudstash\Point\Helper\Arr;
use Cloudstash\Point\Helper\Routing;
use Cloudstash\Point\Helper\Str;
use Cloudstash\Point\HTTP\HttpRequest;
use Cloudstash\Point\HTTP\Server;
use Cloudstash\Point\HTTP\Uri;
use Doctrine\Instantiator\Instantiator;

class Dispatcher
{
    const PREG_VARIABLE = '/^<([A-Za-z0-9_]+)(:|@)([A-Za-z0-9_\-]+)>$/im';

    const MODE_SET = ':';
    const MODE_FILTER = '@';

    /**
     * @var array[]
     */
    protected $collection = [];
    /**
     * @var callable
     */
    protected $defaultRoute = null;
    /**
     * @var callable
     */
    protected $notFoundRoute = null;

    /**
     * @var callback[]
     */
    protected $filters = [];

    protected static $instance = null;

    public static $defaultAvailableMethods = ['GET', 'POST'];

    /**
     * @return Dispatcher
     */
    public static function Instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __clone() {}
    protected function __construct() {}

    /**
     * @param string $name
     * @param callback $callback
     */
    public static function registerFilter($name, $callback)
    {
        if (is_callable($callback)) {
            self::Instance()->filters[$name] = $callback;
        }
    }

    /**
     * @return string
     */
    protected function getCurrentRequestMethod()
    {
        return Server::Instance()
            ->getRequestMethod();
    }

    /**
     * @param callable $route
     * @return $this
     */
    public function registerDefault($route)
    {
        $this->defaultRoute = $route;

        return $this;
    }

    /**
     * @param callable $route
     * @return $this
     */
    public function registerNotFound($route)
    {
        $this->notFoundRoute = $route;

        return $this;
    }

    /**
     * @param $method
     * @return string[]
     */
    protected function prepareMethod($method)
    {
        if (is_array($method)) {
            return $method;
        }

        if (!$method) {
            return self::$defaultAvailableMethods;
        }

        $method = explode(',', $method);

        return array_map(function($item) {
            return Str::Trim($item);
        }, Arr::StripEmpty($method));
    }

    /**
     * @param string $method
     * @param Route|string $pattern
     * @param string|callback $handler
     * @return $this
     */
    public function register($method, $pattern, $handler)
    {
        $pattern = Routing::explodeUrl($pattern);

        if ($pattern instanceof Route) {
            $route = $pattern;
        } else {
            $route = new Route($handler);
            foreach ($pattern as $segment) {
                if (preg_match(self::PREG_VARIABLE, $segment, $matches)) {
                    $var = Arr::get($matches, 1);
                    $type = Arr::get($matches, 2);
                    $value = Arr::get($matches, 3);

                    if ($type == self::MODE_SET) {
                        $route->registerVar($var, $value);
                        continue;
                    }

                    $route->registerVar($var, Arr::get($this->filters, $value, $value));

                    continue;
                }

                $route->registerBlock($segment);
            }
        }

        $this->collection[] = [
            'method' => $this->prepareMethod($method),
            'route' => $route
        ];

        return $this;
    }

    protected function callAction($handler)
    {
        $variables = [];

        if ($handler instanceof Route) {
            $variables = $handler->getValues();
            $handler = $handler->getHandler();
        }

        HttpRequest::Instance()->setVariables($variables);

        if (is_callable($handler)) {
            return call_user_func_array($handler, []);
        }

        if (preg_match('/^([A-z0-9_\.]+)@([A-z0-9_]+)$/im', $handler, $matches)) {
            $namespace = Arr::get($matches, 1);
            $namespace = explode('.', $namespace);
            $namespace = '\\' . implode('\\', $namespace);

            $action = Arr::get($matches, 2);
            $action = 'action' . ucfirst($action);

            $controller = (new Instantiator())->instantiate($namespace);

            if (method_exists($controller, $action)) {
                return (new \ReflectionMethod($controller, $action))->invoke($controller);
            }
        }
    }

    /**
     * @return Route
     */
    public function handleCurrent()
    {
        $uri = new Uri();

        if ($uri->isRootDirectory()) {
            return $this->callAction($this->defaultRoute);
        }

        $requestPath = $uri->getRequestPath();

        foreach ($this->collection as $index => $routeSettings) {
            /**
             * @var Route $route
             */
            $route = Arr::get($routeSettings, 'route', null);
            $availableMethod = Arr::get($routeSettings, 'method', self::$defaultAvailableMethods);

            if (is_null($route)) {
                continue;
            }

            if (!in_array($this->getCurrentRequestMethod(), $availableMethod)) {
                continue;
            }

            if ($route->isCurrent($requestPath)) {
                return $this->callAction($route);
            }
        }

        return $this->callAction($this->notFoundRoute);
    }
}