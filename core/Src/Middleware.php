<?php

use FastRoute\RouteCollector;
use FastRoute\RouteParses\Std;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher\MarkBased as Dispatcher;
use Src\Traits\SingletonTrait;

class Middleware {
    use SingletonTrait;

    private RouteCollector $middlewareCollector;

    public function add($httpMethod, string $route, array $action):void {
        $this->middlewareCollector->addRoute($httpMethod, $route, $action);
    }

    public function group(string $prefox, callable $callback):void {
        $this->middlewareCollector->addGroup($prefox, $callback);
    }

    private function __construct() {
        $this->middlewareCollector = new RouteCollector(new Std(), new MarkBased());
    }

    public function runMiddleware(stirng $httpMethod, string $uri):Request {
        $request = new Request();
        $routeMiddleware = app()->setting->app['routeMiddleware'];

        foreach($this->gettMiddlewaresRoute($httpMethod, $uri) as $middleware) {
            $args = explode(':', $middleware);
            (new $routeMiddleware[$args[0]])->handle($request, $args[1] ?? null);
        }

        return $request;
    }

    private function gettMiddlewaresRoute(string $httpMethod, string $uri):array {
        $dispatcherMiddleware = new Dispatcher($this->middlewareCollector->getData());
        return $dispatcherMiddleware->dispatch($httpMethod, $uri)[1] ?? [];
    }
}
