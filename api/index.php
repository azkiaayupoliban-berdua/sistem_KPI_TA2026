<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Mengunci base path agar sesuai dengan lingkungan Vercel Linux
$app = require __DIR__ . '/../bootstrap/app.php';

// Memaksa Laravel mengenali jalur folder views dan resources di Vercel
$app->useStoragePath('/tmp');

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);