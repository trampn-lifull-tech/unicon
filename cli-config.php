<?php

define('LARAVEL_START', microtime(true));

$loader = require __DIR__ . '/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);

/** @var Illuminate\Contracts\Http\Kernel $kernel */
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

try {
    $controller = new \App\Http\Controllers\ApiController;
    $entityManager = new \Chaos\Support\Orm\EntityManagerFactory;

    return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(
        $entityManager($controller->getContainer(), null, $controller->getVars()->getContent())
    );
} catch (Exception $ex) {
    //
}
