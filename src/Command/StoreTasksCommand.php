<?php

namespace App\Command;

use App\Entity\Developer;
use App\Entity\Task;
use App\Utils\Task\Factory\ProviderFactory;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreTasksCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:store-tasks';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Storing tasks to database.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to storing tasks to database.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deleteTasks();
        $tasks = $this->getTasks();
        $this->insertTasks($tasks);
        return 0;
    }

    /**
     * @throws Exception
     */
    private function deleteTasks(): void
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->delete('task');
        $qb->execute();
        $this->entityManager->flush();
    }

    private function getTasks(): ?array
    {
        $tasksToMerge = [];
        $providers = [
            'Provider1',
            'Provider2',
        ];
        foreach ($providers as $provider) {
            $providerObj = ProviderFactory::create($provider);
            $tasksToMerge[] = $providerObj->getTransformedTasks($providerObj->fetchTasks());
        }
        return array_merge([], ...$tasksToMerge);
    }

    private function insertTasks($tasks): void
    {
        foreach ($tasks as $task) {
            /** @var $task Task */
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();
    }
}