<?php

namespace App\Command;

use App\Entity\Developer;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedDeveloperCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:seed-developer';

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
            ->setDescription('Seeding developers.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to seeding developers.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deleteDevelopers();
        $this->insertDevelopers();
        return 0;
    }

    /**
     * @throws Exception
     */
    private function deleteDevelopers(): void
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->delete('developer');
        $qb->execute();
        $this->entityManager->flush();
    }

    private function insertDevelopers(): void
    {
        $developer1 = new Developer();
        $developer1->setName('DEV1');
        $developer1->setWorkCapacityPerHour(1.00);

        $developer2 = new Developer();
        $developer2->setName('DEV2');
        $developer2->setWorkCapacityPerHour(2.00);

        $developer3 = new Developer();
        $developer3->setName('DEV3');
        $developer3->setWorkCapacityPerHour(3.00);

        $developer4 = new Developer();
        $developer4->setName('DEV4');
        $developer4->setWorkCapacityPerHour(4.00);

        $developer5 = new Developer();
        $developer5->setName('DEV5');
        $developer5->setWorkCapacityPerHour(5.00);

        $developers = [
            $developer1,
            $developer2,
            $developer3,
            $developer4,
            $developer5,
        ];
        foreach ($developers as $developer) {
            $this->entityManager->persist($developer);
        }
        $this->entityManager->flush();
    }
}