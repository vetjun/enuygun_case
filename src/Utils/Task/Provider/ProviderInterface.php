<?php


namespace App\Utils\Task\Provider;


interface ProviderInterface
{
    public function getEndpoint(): ?string;
    public function getName(): ?string;
    public function fetchTasks(): ?array;
    public function getTransformedTasks($tasks): ?array;
}