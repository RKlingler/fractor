<?php

declare(strict_types=1);

use a9f\Fractor\Application\Contract\FileProcessor;
use a9f\Fractor\Application\FractorRunner;
use a9f\Fractor\Configuration\AllowedFileExtensionsResolver;
use a9f\Fractor\Configuration\SkipConfigurationFactory;
use a9f\Fractor\Configuration\ValueObject\SkipConfiguration;
use a9f\Fractor\Differ\ConsoleDiffer;
use a9f\Fractor\Differ\Contract\Differ;
use a9f\Fractor\FractorApplication;
use a9f\Fractor\Rules\RulesProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    $services->load('a9f\\Fractor\\', __DIR__ . '/../src/')
        ->exclude(
            [
                __DIR__ . '/../src/Console/Output',
                __DIR__ . '/../src/Testing',
                __DIR__ . '/../src/ValueObject',
                __DIR__ . '/../src/**/ValueObject',
            ]
        );

    $containerBuilder->registerAttributeForAutoconfiguration(
        AsCommand::class,
        static function (ChildDefinition $definition, AsCommand $attribute): void {
            $commands = explode('|', $attribute->name);
            $hidden = false;
            $name = array_shift($commands);

            if ($name === '') {
                // Symfony AsCommand attribute encodes hidden flag as an empty command name
                $hidden = true;
                $name = array_shift($commands);
            }

            if ($name === null) {
                // This happens in case no name and no aliases are given
                return;
            }

            $definition->addTag(
                'console.command',
                [
                    'command' => $name,
                    'description' => $attribute->description,
                    'hidden' => $hidden,
                ]
            );

            foreach ($commands as $name) {
                $definition->addTag(
                    'console.command',
                    [
                        'command' => $name,
                        'hidden' => $hidden,
                        'alias' => true,
                    ]
                );
            }
        }
    );

    $services->set('parameter_bag', ContainerBag::class)
        ->args([service('service_container')])
        ->alias(ContainerBagInterface::class, 'parameter_bag')
        ->alias(ParameterBagInterface::class, 'parameter_bag');

    $services->alias(Differ::class, ConsoleDiffer::class);
    $services->set(FractorApplication::class)->call('setCommandLoader', [service('console.command_loader')]);
    $services->set(SkipConfiguration::class)->factory([service(SkipConfigurationFactory::class), 'create']);
    $services->set(FractorRunner::class)->arg('$processors', tagged_iterator('fractor.file_processor'));
    $services->set(AllowedFileExtensionsResolver::class)->arg('$processors', tagged_iterator('fractor.file_processor'));
    $services->set(Filesystem::class);
    // RulesProvider must be wired individually for each processor to ensure the correct rules base class is set
    $services->set(RulesProvider::class)->autowire(false);

    $containerBuilder->registerForAutoconfiguration(FileProcessor::class)->addTag('fractor.file_processor');
};
