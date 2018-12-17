<?php

define('LARAVEL_START', microtime(true));

$loader = require __DIR__ . '/vendor/autoload.php';
Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);

/** @var Illuminate\Contracts\Http\Kernel $kernel */
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

try {
    $controller = new Chaos\Module\Common\LaravelController;
    $entityManager = (new \Chaos\Common\Mapper\EntityManagerFactory)
        ->__invoke(null, null, $controller->getVars()->getContent());

    return Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
} catch (Exception $ex) {
    //
}
