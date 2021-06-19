<?php

namespace App\Controller;

use App\Repository\DeveloperRepository;
use App\Repository\TaskRepository;
use App\Utils\Task\Plan\Planner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class TaskController extends AbstractController
{

    /**
     * @Route("/tasks/planning", name="app_task_planning")
     * @return Response
     * @throws \JsonException
     */
    public function plan(SerializerInterface $serializer, TaskRepository $taskRepository, DeveloperRepository $developerRepository): Response
    {
        $tasks = $taskRepository->findAll();
        $developers = $developerRepository->findAll();
        $planner = new Planner($tasks, $developers);

        $planning = $planner->getPlanning();
        $dataJson = $serializer->serialize($planning, JsonEncoder::FORMAT);
        return new JsonResponse($dataJson, Response::HTTP_OK, [], true);
    }
}