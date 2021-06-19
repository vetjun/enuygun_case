<?php
namespace App\Utils\Task\Factory;

use App\Utils\Task\Provider\ProviderInterface;

class ProviderFactory
{
    public static function create($provider, $params = []): ProviderInterface
    {
        $className = 'App\\Utils\\Provider\\' . $provider;
        if (!class_exists($className)) {
            throw new \RuntimeException('There is no matching provider for -> ' . $provider);
        }

        return new $className($params);
    }

    public static function getProviders(): array
    {
        return [
            'Provider1',
            'Provider2',
        ];
    }
}