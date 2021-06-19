<?php

namespace App\Controller;

use App\Repository\DeveloperRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @return Response
     */
    public function index(TaskRepository $taskRepository, DeveloperRepository $developerRepository): Response
    {
        $tasks = $taskRepository->findAll();
        $developers = $developerRepository->findAll();
        return $this->render('index.html.twig', ['tasks' => $tasks, 'developers' => $developers]);
    }
}