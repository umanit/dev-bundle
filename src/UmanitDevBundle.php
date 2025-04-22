<?php

declare(strict_types=1);

namespace Umanit\DevBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Umanit\DevBundle\Foundry\CommandDatabaseResetter;
use Zenstruck\Foundry\ORM\ResetDatabase\OrmResetter;

final class UmanitDevBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        // @formatter:off
        $definition
            ->rootNode()
            ->children()
                ->scalarNode('database_resetter_command')
                    ->info(
                        <<<TXT
Une commande symfony à exécuter pour le ResetDatabase de foundry. Laisser null pour ne pas utiliser le resetter
TXT
                    )
                    ->defaultNull()
                ->end()
            ->end()
        ;
        // @formatter:on
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $command = $config['database_resetter_command'];
        if ($command !== null) {
            $builder
                ->register('umanit_dev.orm_resetter', CommandDatabaseResetter::class)
                ->setDecoratedService(OrmResetter::class)
                ->setArguments([new Reference('doctrine'), $command])
            ;
        }
    }
}
