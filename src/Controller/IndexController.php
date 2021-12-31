<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Repository\RepositoryResponse;
use App\Repository\TodoListRepository;
use App\Service\PlannerService;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(TodoListRepository $todoListRepository, PlannerService $plannerService)
    {
        $developersMain = $plannerService->developerCreate(5);

        /** @var RepositoryResponse $allToDoResponse */
        $allToDoResponse = $todoListRepository->findAll();
        if ($allToDoResponse->isSuccess()) {
            /** @var TodoList $toDo */
            $allTask = $plannerService->taskReGroup($allToDoResponse->getResponse());
            $data = $plannerService->planner($allTask,$developersMain);
            $totalData = $plannerService->totalCalculator($data);
        }
        return $this->render('index.html.twig', [
            // this array defines the variables passed to the template,
            // where the key is the variable name and the value is the variable value
            // (Twig recommends using snake_case variable names: 'foo_bar' instead of 'fooBar')
            'tasks' => $data['weekArray'],
            'estimatedTotalTime' => $data['estimatedTotalTime'],
            'totalData' => $totalData
        ]);
    }

}
