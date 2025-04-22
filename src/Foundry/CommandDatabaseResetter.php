<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Foundry;

use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Zenstruck\Foundry\ORM\ResetDatabase\OrmResetter;

final readonly class CommandDatabaseResetter implements OrmResetter
{
    /**
     * @var array<string, string>
     */
    private array $databases;

    public function __construct(
        private Registry $registry,
        private string $resetCommand
    ) {
        $databases = [];
        /** @var Connection $connection */
        foreach ($this->registry->getConnections() as $name => $connection) {
            // Nécessaire pour éviter d'interférer avec les transactions
            StaticDriver::setKeepStaticConnections(false);
            try {
                /** @var AbstractPlatform $platform */
                $platform = $connection->getDatabasePlatform();
                $databases[$name] = $platform->quoteStringLiteral($connection->getDatabase());
                $connection->close();
            } catch (\Exception) {
                // Si on n'arrive pas à récupérer le nom de la base de donnée de manière officielle,
                // On se rabat sur le fonctionnement interne de doctrine (moins stable)
                /** @var array{dbname: string} $params */
                $params = $connection->getParams();
                $databases[$name] = \sprintf("\'%s\'", $params['dbname']);
            }
            StaticDriver::setKeepStaticConnections(true);
        }

        $this->databases = $databases;
    }

    public function resetBeforeFirstTest(KernelInterface $kernel): void
    {
        // Nécessaire pour éviter d'interférer avec les transactions
        StaticDriver::setKeepStaticConnections(false);
        $this->resetDatabase($this->getApplication($kernel));
        StaticDriver::setKeepStaticConnections(true);
    }

    private function resetDatabase(Application $application): void
    {
        $this->dropConnections($application);
        $this->run($application, $this->resetCommand);
    }

    public function dropConnections(Application $application): void
    {
        $databases = array_map(static fn(string $name) => \sprintf("\'%s\'", $name), $this->databases);
        $databases = implode(', ', $databases);

        $sql = \sprintf(
            'SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname IN (%s) AND pid <> pg_backend_pid()',
            $databases
        );
        $this->run($application, \sprintf("dbal:run-sql '%s'", $sql), true);
    }

    private function run(Application $application, string $command, bool $allowFailure = false): int
    {
        $output = new BufferedOutput();
        $exitCode = $application->run(new StringInput($command), $output);
        if (!$allowFailure && $exitCode !== Command::SUCCESS) {
            throw new \RuntimeException(\sprintf('Error running %s: %s', $command, $output->fetch()));
        }

        return $exitCode;
    }

    private function getApplication(KernelInterface $kernel): Application
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        return $application;
    }

    public function resetBeforeEachTest(KernelInterface $kernel): void
    {
    }
}
