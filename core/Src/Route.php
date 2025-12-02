<?php
namespace Src;

use Error;

use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher\MarkBased as Dispatcher;
use Src\Traits\SingletonTrait;

class Route {
    use SingletonTrait;
    private string $currentRoute = '';
    private $currentHttpMethod;
    private string $prefix = '';
    private RouteCollector $routeCollector;
    private static array $routes = [];

    public function setPrefix($value):self {
        $this->prefix = $value;
        return $this;
    }

    public static function add($httpMethod, string $route, array $action):self {
        self::single()->routeCollector->addRoute($httpMethod, $route, $action);
        self::single()->currentHttpMethod = $httpMethod;
        self::single()->currentRoute = $route;
        return self::single();
    }

    public static function group(string $prefix, callable $callback):void {
        self::single()->routeCollector-addGroup($prefix, $callback);
        Middleware::single()->group($prefix, $callback);
    }

    public function start():void {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $pos = strpos($uri, '?');
        if ($pos) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);
        $uri = substr($uri, strlen($this->prefix));

        $dispatcher = new Dispatcher($this->routeCollector->getData());

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new Error('NOT FOUND');
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new Error('METHOD NOT ALLOWED');
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = array_values($routeInfo[2]);
                $vars[] = Middleware::single()->go($httpMethod, $uri, new Request());
                $class = $handler[0];
                $action = $handler[1];
                call_user_func([new $class, $action], ...$vars);
                break;
        }
    }

    public function redirect(string $url):void {
        header('Location: ' . $this->getUrl($url));
    }

    public function getUrl(string $url):string {
        return $this->prefix . $url;
    }

    public function __construct() {
        $this->routeCollector = new RouteCollector(new Std(), new MarkBased());
    }

    public function middleware(...$middlewares):self {
        Middleware::single()->add($this->currentHttpMethod, $this->currentRoute, $middlewares);
        return $this;
    }
}