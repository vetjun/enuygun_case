<?php
namespace App\Utils\Task\Factory;

use App\Utils\Task\Provider\Provider1;
use App\Utils\Task\Provider\Provider2;
use App\Utils\Task\Provider\ProviderInterface;

class ProviderFactory
{
    public static function create($provider, $params = []): ProviderInterface
    {
        switch ($provider) {
            case 'Provider1':
                return new Provider1($params);
            case 'Provider2':
                return new Provider2($params);
            default:
                throw new \RuntimeException('There is no matching provider for -> ' . $provider);
        }
    }
}