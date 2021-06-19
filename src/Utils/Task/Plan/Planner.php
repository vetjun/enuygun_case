<?php

namespace App\Utils\Task\Plan;

use App\Entity\Developer;
use App\Entity\Task;

class Planner
{
    private $tasks;
    private $developers;
    private $planning = [];

    public function __construct($tasks, $developers)
    {
        $this->tasks = $tasks;
        $this->developers = $developers;

    }

    public function getPlanning(): ?array
    {
        $sortedTasks = $this->sortTasksByWeight();
        $this->apply($sortedTasks);
        $this->planning['minHoursOfExecution'] = $this->getActualSizeOfPlanning();
        return $this->planning;

    }

    private function sortTasksByWeight(): array
    {
        usort($this->tasks, [$this, 'sortByWeight']);
        return $this->tasks;
    }

    private function sortByWeight($a, $b): int
    {
        if ($this->getWeight($b) < $this->getWeight($a)) {
            return -1;
        }

        if ($this->getWeight($b) === $this->getWeight($a)) {
            return 0;
        }

        return 1;
    }

    private function getWeight($task): float
    {
        /** @var $task Task */
        return (float)$task->getTime() * (float)$task->getLevel();
    }

    private function apply(array $sortedTasks): void
    {
        $this->planning['developers'] = [];
        $this->planning['totalWeight'] = 0;
        foreach ($sortedTasks as $sortedTask) {
            $this->planning['totalWeight'] += $sortedTask->getTime() * $sortedTask->getLevel();
            $allocatedTask = $this->allocateTask($sortedTask);

            $this->allocateDeveloper($allocatedTask);
            if ($allocatedTask['remains'] > 0) {
                $this->allocateRemains($allocatedTask);
            }
        }
    }

    private function allocateTask(mixed $sortedTask): array
    {
        /** @var $sortedTask Task */
        $taskWeight = $sortedTask->getTime() * $sortedTask->getLevel();
        $trialPlanning = [];
        $minExpectedSize = PHP_INT_MAX;
        foreach ($this->developers as $developer) {
            /** @var $developer Developer */
            $capacity = $developer->getWorkCapacityPerHour();
            if ($taskWeight < $capacity) {
                continue;
            }
            $actualSizeOfDeveloper = $this->getActualSizeOfPlanningByDeveloper($developer);

            $divided = (int)((int)$taskWeight / (int)$capacity);
            $allocatedWeight = (int)($capacity * $divided);
            $remains = $taskWeight - $allocatedWeight;


            $trialPlanning[$developer->getName()] = [];
            $trialPlanning[$developer->getName()]['developer'] = $developer;
            $trialPlanning[$developer->getName()]['task'] = $sortedTask;
            $trialPlanning[$developer->getName()]['allocatedWeight'] = $allocatedWeight;
            $trialPlanning[$developer->getName()]['taskWeight'] = $taskWeight;
            $trialPlanning[$developer->getName()]['divided'] = $divided;
            $trialPlanning[$developer->getName()]['remains'] = $remains;
            $trialPlanning[$developer->getName()]['expectedSize'] = $actualSizeOfDeveloper + $divided;
        }
        $minTrial = $trialPlanning[0] ?? [];
        foreach ($trialPlanning as $developerKey => $trial) {
            if ($trial['expectedSize'] <= $minExpectedSize) {
                $minExpectedSize = $trial['expectedSize'];
                $minTrial = $trialPlanning[$developerKey];
            }
        }
        return $minTrial;
    }

    private function allocateDeveloper($allocatedTask): void
    {
        $developerName = $allocatedTask['developer']->getName();
        $this->planning['developers'][$developerName] = $this->planning['developers'][$developerName] ?? [];
        $this->planning['developers'][$developerName]['name'] = $developerName;
        $this->planning['developers'][$developerName]['developer'] = $allocatedTask['developer'];
        $this->planning['developers'][$developerName]['tasks'] = $this->planning['developers'][$developerName]['tasks'] ?? [];
        $this->planning['developers'][$developerName]['actualSizeOfTime'] = $this->planning['developers'][$developerName]['actualSizeOfTime'] ?? 0;
        $this->planning['developers'][$developerName]['actualSizeOfTime'] += $allocatedTask['divided'];
        $this->planning['developers'][$developerName]['tasks'][] = [
            'name' => $allocatedTask['task']->getName(),
            'totalWeight' => $allocatedTask['taskWeight'],
            'allocatedWeight' => $allocatedTask['allocatedWeight'],
            'allocatedTime' => $allocatedTask['divided']
        ];
    }

    private function getActualSizeOfPlanning()
    {
        $actualSizesOfTime = [];
        foreach ($this->planning['developers'] as $developer) {
            $actualSizesOfTime[] = $developer['actualSizeOfTime'] ?? 0.00;
        }
        return max($actualSizesOfTime);

    }

    private function getActualSizeOfPlanningByDeveloper($developer)
    {
        /** @var  $developer Developer */
        foreach ($this->planning['developers'] as $developerArr) {
            if ($developerArr['name'] === $developer->getName()) {
                return $developerArr['actualSizeOfTime'] ?? 0.00;
            }
        }
        return 0.00;
    }

    private function allocateRemains(array $allocatedTask): void
    {
        $remains = $allocatedTask['remains'];
        while ($remains > 0) {
            $trialPlanning = [];
            $minExpectedSize = PHP_INT_MAX;
            foreach ($this->developers as $developer) {
                /** @var $developer Developer */
                $capacity = $developer->getWorkCapacityPerHour();
                $actualSize = $this->getActualSizeOfPlanningByDeveloper($developer);
                if ($capacity > $remains) {
                    continue;
                }
                $divided = (int)((int)$remains / (int)$capacity);
                $allocatedWeight = (int)($capacity * $divided);
                $newRemains = $remains - $allocatedWeight;

                $trialPlanning[$developer->getName()] = [];
                $trialPlanning[$developer->getName()]['developer'] = $developer;
                $trialPlanning[$developer->getName()]['task'] = $allocatedTask['task'];
                $trialPlanning[$developer->getName()]['taskWeight'] = $allocatedTask['taskWeight'];
                $trialPlanning[$developer->getName()]['allocatedWeight'] = $allocatedWeight;
                $trialPlanning[$developer->getName()]['divided'] = $divided;
                $trialPlanning[$developer->getName()]['remains'] = $newRemains;
                $trialPlanning[$developer->getName()]['expectedSize'] = $actualSize + $divided;
            }
            $minTrial = $trialPlanning[0] ?? [];
            foreach ($trialPlanning as $developerKey => $trial) {
                if ($trial['expectedSize'] <= $minExpectedSize) {
                    $minExpectedSize = $trial['expectedSize'];
                    $minTrial = $trialPlanning[$developerKey];
                }
            }
            $this->allocateDeveloper($minTrial);
            $remains = $minTrial['remains'];
        }
    }
}