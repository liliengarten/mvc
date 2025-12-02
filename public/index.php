<?php
declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);


try {
    session_start();
    $app = require_once __DIR__ . '/../core/bootstrap.php';
    $app->run();
} catch (\Throwable $exception) {
    echo "<pre>";
    print_r($exception);
    echo "</pre>";
}