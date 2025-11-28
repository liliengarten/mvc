<?php
namespace Src;

use Error;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;
use Src\Auth\Auth;

class Application {
    private Settings $settings;
    private Route $route;
    private Capsule $dbManager;
    private Auth $auth;
    public function __construct(Settings $settings) {
        $this->settings = $settings;
        $this->route = new Route();
        $this->dbManager = new Capsule();
        $this->auth = new $this->settings->app['auth'];
        $this->dbRun();
        $this->auth::init(new $this->settings->app['identity']);
    }

    public function __get($key) {
        switch ($key) {
            case 'settings':
                return $this->settings;
            case 'route':
                return $this->route;
            case 'auth':
                return $this->auth;
            default:
                throw new Error('Accessing a non-existing property: ' . $key);
        }
    }

    private function dbRun() {
        $this->dbManager->addConnection($this->settings->getDbSettings());
        $this->dbManager->setEventDispatcher(new Dispatcher(new Container));
        $this->dbManager->setAsGlobal();
        $this->dbManager->bootEloquent();
    }

    public function run():void {
        $this->route->start();
    }
}