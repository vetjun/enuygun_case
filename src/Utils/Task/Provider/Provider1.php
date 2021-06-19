<?php

namespace App\Utils\Task\Provider;


use App\Entity\Task;

class Provider1 extends AbstractProvider
{

    protected $endpoint = 'http://www.mocky.io/v2/5d47f24c330000623fa3ebfa';
    protected $name = 'Provider1';

    public function getTransformedTasks($tasks): ?array
    {
        $transformedTasks = [];
        foreach ($tasks as $task) {
            $tempTask = new Task();
            $tempTask->setProvider($this->getName());
            $tempTask->setName($task['id'] ?? 'Unnamed');
            $tempTask->setTime($task['sure'] ?? 0.00);
            $tempTask->setLevel($task['zorluk'] ?? 1.00);
            $transformedTasks[] = $tempTask;
        }
        return $transformedTasks;
    }
}