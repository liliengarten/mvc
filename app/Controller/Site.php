<?php
namespace Controller;
use Src\View;
class Site {
    public function index():string {
        return new View('site.hello', ['message' => 'hello working']);
    }

    public function hello():string {
        return new View('site.hello', ['message' => 'hello working']);
    }
}