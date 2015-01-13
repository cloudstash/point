<?php

namespace Cloudstash\Point\Routing;

use Cloudstash\Helper\Arr;
use Cloudstash\Helper\Str;
use Cloudstash\Point\Helper\Routing;
use Cloudstash\Point\HTTP\HttpRequest;
use Cloudstash\Point\HTTP\Server;
use Cloudstash\Point\HTTP\Uri;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\Instantiator\Instantiator;

class Dispatcher
{
    const PREG_VARIABLE = '/^<([A-Za-z0-9_\-]+)(@*)([A-Za-z0-9_\-]*)>$/im';
    const PREG_VAR_IN_HANDLER_STRING = '/\#{([A-z0-9\-_]+)}/';
    const PREG_HANDLER_PATTERN = '/^([A-z0-9_\-\.]+)@([A-z0-9_\-]+)$/im';

    const PREFIX_ACTION_METHOD = 'action';

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
     * @param callable $callable
     * @throws \Exception
     */
    public static function registerFilter($name, $callable)
    {
        if (!is_callable($callable)) {
            throw new \Exception("Not callable function to use in filter");
        }

        self::Instance()->filters[$name] = $callable;
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
     * @param string $name
     * @param array|string $method
     * @param Route|string $pattern
     * @param string|callback $handler
     * @return $this
     */
    public function register($name, $pattern, $handler, $method = ['GET', 'POST'])
    {
        $pattern = Routing::explodeUrl($pattern);

        if ($pattern instanceof Route) {
            $route = $pattern;
        } else {
            $route = new Route($handler);
            foreach ($pattern as $segment) {
                if (preg_match(self::PREG_VARIABLE, $segment, $matches)) {
                    $var = Arr::get($matches, 1);
                    $value = Arr::get($matches, 3);

                    $route->registerVar($var, Arr::get($this->filters, $value, $value));

                    continue;
                }

                $route->registerBlock($segment);
            }
        }

        $this->collection[$name] = [
            'method' => $this->prepareMethod($method),
            'route' => $route
        ];

        return $this;
    }

    protected function callAction($handler, $throw = false)
    {
        $variables = [];

        if ($handler instanceof Route) {
            $variables = $handler->getValues();
            $handler = $handler->getHandler();
        }

        HttpRequest::Instance()->setVariables($variables);

        // if right callable - use it
        if (is_callable($handler)) {
            return call_user_func_array($handler, $variables);
        } else {
            // Replace all variables in handler (if need)
            $handler = preg_replace_callback(self::PREG_VAR_IN_HANDLER_STRING, function($matches) use ($variables) {
                $var_name = Arr::get($matches, 1);
                return Arr::get($variables, $var_name, $var_name);
            }, $handler);
        }

        // or extract it from pattern
        if (preg_match(self::PREG_HANDLER_PATTERN, $handler, $matches)) {
            $namespace = Arr::get($matches, 1);
            $namespace = explode('.', $namespace);
            $namespace = '\\' . implode('\\', $namespace);

            $action = Arr::get($matches, 2);
            $action = self::PREFIX_ACTION_METHOD . ucfirst($action);

            try
            {
                $controller = (new Instantiator())->instantiate($namespace);

                if (method_exists($controller, $action)) {
                    return (new \ReflectionMethod($controller, $action))->invokeArgs($controller, $variables);
                }
            }
            catch (InvalidArgumentException $e)
            {
                return $this->callAction($this->notFoundRoute, true);
            }
        }

        if ($throw) {
            throw new \Exception('What a fuckhell');
        }

        return $this->callAction($this->notFoundRoute, true);
    }

    /**
     * @param $name
     * @param array $params
     * @return null|string
     */
    public function toUrl($name, array $params = [])
    {
        $route = Arr::get($this->collection, $name, []);
        $route = Arr::get($route, 'route');

        if (!($route instanceof Route)) {
            return null;
        }

        return $route->toUrl($params);
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

        foreach ($this->collection as $routeName => $routeSettings) {
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