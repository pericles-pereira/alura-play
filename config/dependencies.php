<?php 

use DI\ContainerBuilder;
use League\Plates\Engine;

$builder = new ContainerBuilder();
$builder->addDefinitions([
    PDO::class => function (): PDO {
        return new PDO('mysql:host=localhost;dbname=mvc1', 'root', '');
    },
    \League\Plates\Engine::class => function () {
        $templatePath = __DIR__ . '/../views';
        return new Engine($templatePath);
    }
]);

/** @var ContainerInterface $container */
$container = $builder->build();

return $container;