<?php
namespace App\Utils\Task\Provider;

use App\Entity\Task;

class Provider2 extends AbstractProvider
{

    protected $endpoint = 'http://www.mocky.io/v2/5d47f235330000623fa3ebf7';
    protected $name = 'Provider2';

    public function getTransformedTasks($tasks): ?array
    {
        $transformedTasks = [];
        foreach ($tasks as $task) {
            $taskAssoc = reset($task);
            if (!$taskAssoc) {
                continue;
            }
            $taskName = key($task);
            $tempTask = new Task();
            $tempTask->setProvider($this->getName());
            $tempTask->setName($taskName ?? 'Unnamed');
            $tempTask->setTime($taskAssoc['estimated_duration'] ?? 0.00);
            $tempTask->setLevel($taskAssoc['level'] ?? 1.00);
            $transformedTasks[] = $tempTask;
        }
        return $transformedTasks;
    }

}