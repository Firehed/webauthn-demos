<?php

declare(strict_types=1);

// Doctrine Setup

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\{ApcuAdapter, ArrayAdapter};

use function Firehed\Container\env;

return [
    'database_url' => env('DATABASE_URL'),

    'localPsr6Cache' => function ($c): CacheItemPoolInterface {
        if ($c->get('isDevMode')) {
            return new ArrayAdapter();
        } else {
            return new ApcuAdapter();
        }
    },
    // Attribute driver docs:
    // https://www.doctrine-project.org/projects/doctrine-orm/en/2.10/reference/attributes-reference.html
    MappingDriver::class => fn() => new AttributeDriver(['src']),

    EntityManagerInterface::class => function ($c) {
        $isDevMode = $c->get('isDevMode');

        $proxyDir = '.generated/doctrine-proxies';

        $cache = DoctrineProvider::wrap($c->get('localPsr6Cache'));

        $config = Setup::createConfiguration(
            isDevMode: $isDevMode,
            proxyDir: $proxyDir,
            cache: $cache,
        );

        $config->setMetadataDriverImpl($c->get(MappingDriver::class));
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        $config->setSQLLogger($c->get(SQLLogger::class));

        // https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
        $connection = [
            'driver' => 'pdo_sqlite',
            // This is set automatically from above, but provides a useful starting
            // reference point for debugging
            'driverClass' => \Doctrine\DBAL\Driver\PDO\SQLite\Driver::class,
            'driverOptions' => [],

            'url' => $c->get('database_url'),
            // In the future for primary/replica support, set the following:
            // 'wrapperClass' => \Doctrine\DBAL\Connections\PrimaryReadReplicaConnection::class,
            // 'primary' => [
            //     'url' => '...',
            // ],
            // 'replica' => [
            //     [
            //         'url' => '...',
            //     ],
            // ],

        ];

        return EntityManager::create($connection, $config);
    },

    SQLLogger::class => function ($c) {
        $logger = $c->get(LoggerInterface::class);
        return new class ($logger) implements SQLLogger
        {
            private ?int $start = null;
            public function __construct(private LoggerInterface $logger)
            {
            }
            public function startQuery($sql, $params = null, $types = null)
            {
                $this->logger->debug('START QUERY: {sql}', ['sql' => $sql]);
                if ($params) {
                    $this->logger->debug(var_export($params, true));
                }
                $this->start = \hrtime(true);
            }
            public function stopQuery()
            {
                $duration = \hrtime(true) - $this->start;
                $this->start = null;
                $this->logger->debug(sprintf('Finished in %0.3fms', $duration / 1_000_000));
            }
        };
    },
];
