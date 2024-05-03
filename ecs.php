<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/vendor/umbrellio/code-style-php/umbrellio-cs.php');

    $services = $containerConfigurator->services();

    $services->set(PhpUnitTestAnnotationFixer::class)
        ->call('configure', [[
            'style' => 'annotation',
        ]]);

    $services->set(DeclareStrictTypesFixer::class);

    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure', [[
            'default' => 'single_space',
        ]]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set('cache_directory', '.ecs_cache');
    $parameters->set('skip', [
        'PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer' => null,
    ]);

    $parameters->set('exclude_files', ['vendor/*', 'database/*']);
};
