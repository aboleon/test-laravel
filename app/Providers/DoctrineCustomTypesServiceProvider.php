<?php

namespace App\Providers;

use Doctrine\DBAL\Exception;
use Illuminate\Support\ServiceProvider;
use Doctrine\DBAL\Types\Type;
use App\Doctrine\Type\TimestampType;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;

class DoctrineCustomTypesServiceProvider extends ServiceProvider
{
    /**
     * @throws Exception
     */
    public function boot()
    {
        // Register the custom timestamp type
        if (!Type::hasType('timestamp')) {
            Type::addType('timestamp', TimestampType::class);
        }

        // Create Doctrine connection manually for DBAL v4
        $config = config('database.connections.' . config('database.default'));

        $connectionParams = [
            'dbname' => $config['database'],
            'user' => $config['username'],
            'password' => $config['password'],
            'host' => $config['host'],
            'port' => $config['port'] ?? 3306,
            'driver' => 'pdo_mysql',
        ];

        try {
            $connection = DriverManager::getConnection($connectionParams, new Configuration());
            $platform = $connection->getDatabasePlatform();

            // Register the type mapping
            $platform->registerDoctrineTypeMapping('timestamp', 'timestamp');

            // Mark as commented type (optional, for schema introspection)
            if (method_exists($platform, 'markDoctrineTypeCommented')) {
                $platform->markDoctrineTypeCommented(Type::getType('timestamp'));
            }
        } catch (Exception $e) {
            // Log the error or handle gracefully
            \Log::warning('Failed to register Doctrine custom type: ' . $e->getMessage());
        }
    }

    public function register()
    {
        //
    }
}
