<?php

declare(strict_types=1);

$repository = \Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
    ->addAdapter(\Dotenv\Repository\Adapter\PutenvAdapter::class)
    ->immutable()
    ->make();
\Dotenv\Dotenv::create($repository, \realpath(__DIR__) . '/../')->load();

$config = require realpath(__DIR__) . '/config.php';
$container = (new \Coderun\Container\ContainerFactory())($config);
//
return $container;
