<?php
namespace Src;

use Error;

class Application {
    private array $providers = [];
    private array $binds = [];
    public function __construct(array $settings = []) {
        $this->addProviders($settings['providers'] ?? []);
        $this->registerProviders();
        $this->bootProviders();
    }

    public function addProviders(array $providers):void {
        foreach ($providers as $key => $class) {
            $this->providers[$key] = new $class($this);
        }
    }

    public function registerProviders():void {
        foreach ($this->providers as $provider) {
            $provider->register();
        }
    }

    public function bootProviders():void {
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    }

    public function bind(string $key, $value):void {
        $this->binds[$key] = $value;
    }

    public function __get($key) {
        if (array_key_exists($key, $this->binds)) {
            return $this->binds[$key];
        }
        throw new Error('Accessing a non-existent property');
    }

    public function run():void {
        $this->route->start();
    }
}