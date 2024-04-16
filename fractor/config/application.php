<?php

use a9f\Fractor\Configuration\FractorConfig;
use a9f\Fractor\FractorApplication;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    $services->load('a9f\\Fractor\\', __DIR__ . '/../src/')
        ->exclude('../src/Configuration/');

    $services->set(FractorConfig::class)
        ->lazy();
};
