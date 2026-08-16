<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = App\Models\User::where('email', 'admin@sunagro.com')->first();
auth()->login($user);

$request = Illuminate\Http\Request::create('/guias/exportar-todas-zip', 'GET');
$response = $kernel->handle($request);
echo $response->headers->get('Content-Type');
