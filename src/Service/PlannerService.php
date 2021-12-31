<?php

namespace App\Service;

use App\Entity\TodoList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PlannerService
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    /** @var ContainerInterface */
    private ContainerInterface $container;

    const WEEKLYWORKTIME = 45;

    /**
     * GameService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }

    public function planner($allTask,$developersMain)
    {
        $developersMain = $this->ascSortArray($developersMain, 'level');
            $allTask['taskList'] = $this->descSortArray($allTask['taskList'], 'maxEstimated');
        $weeklyWorkableTime = 0;
        array_walk($developersMain, function ($developer) use (&$weeklyWorkableTime) {
            $weeklyWorkableTime += $developer['weeklyTime'];
        });

        $estimatedTotalTime = $allTask['totalTime'];

        $weekCalculate = ceil($allTask['totalTime'] / $weeklyWorkableTime);
        $weekArray = [];

        for ($i = 1; $i <= $weekCalculate; $i++) {
            $developers = $developersMain;
            rePlan:
            foreach ($allTask['taskList'] as $indexTask => $task) {
                $developerReCalc = false;
                if ($allTask['totalTime'] < $weeklyWorkableTime) {
                    array_walk($developersMain, function (&$value) use (&$developerReCalc) {
                        if ($value['remainingTime'] == $value['weeklyTime']) {
                            $developerReCalc = true;
                        }
                    });
                    $developers = $this->descSortArray($developers,'orgRemainingTime');
                }

                foreach ($developers as $indexDev => $dev) {
                    $diff = $developerReCalc == false ? ($dev['level'] < $task['level'] ? $task['level'] - $dev['level'] : $dev['level'] - $task['level']) : 0;
                    if ($task['maxEstimated'] <= $dev['remainingTime'] && $dev['level'] && $diff <= 2) {
                        reInject:
                        $developers[$indexDev]['taskList'][] = $task;
                        $developers[$indexDev]['remainingTime'] -= $task['maxEstimated'];
                        $allTask['totalTime'] -= $task['maxEstimated'];
                        $remainingTime = $developers[$indexDev]['remainingTime'];
                        $developers[$indexDev]['orgRemainingTime'] = ($remainingTime/ $dev['level']);
                        $findingTask = $findingTask2 = null;
                        unset($allTask['taskList'][$indexTask]);
                        if ($allTask['totalTime'] > $weeklyWorkableTime) {
                            array_walk($allTask['taskList'], function ($task, $index) use ($findingTask, $findingTask2, $remainingTime) {
                                if ($remainingTime === $task['maxEstimated']) {
                                    $findingTask = $task;
                                } elseif ($remainingTime >= $task['maxEstimated']) {
                                    $findingTask2 = $task;
                                }
                                $indexDev = $index;
                            });
                            if ($findingTask || $findingTask2) {
                                $task = $findingTask ?? $findingTask2;
                                goto reInject;
                            }
                        } else {
                           // $developers = $this->ascSortArray($developers, 'taskList');
                        }
                        if($i == $weekCalculate){
                            $developers = $this->ascSortArray($developers, 'remainingTime');
                        }else{
                            $developers = $this->descSortArray($developers, 'remainingTime');
                        }
                        $allTask['taskList'] = $this->descSortArray($allTask['taskList'], 'maxEstimated');
                        goto rePlan;
                    }
                }
            }
            sort($developers);
            $weekArray[$i] = $developers;
        }
        return ['weekArray' => $weekArray, 'estimatedTotalTime' => $estimatedTotalTime ];
    }

    public function developerCreate($max)
    {
        $developerArray = [];
        for ($i = 1; $i <= $max; $i++) {
            $developerArray[] = [
                'devId' => 'DEV' . $i,
                'level' => $i,
                'weeklyTime' => $i * self::WEEKLYWORKTIME,
                'remainingTime' => $i * self::WEEKLYWORKTIME,
                'taskList' => [],
                'orgRemainingTime' => self::WEEKLYWORKTIME
            ];
        }
        return $developerArray;
    }

    public function taskReGroup($array)
    {
        $allTask = [];
        /** @var TodoList $task */
        foreach ($array as $task)
        {
            $taskTime = $task->getLevel() * $task->getEstimated();
            $allTask['totalTime'] = ($allTask['totalTime'] ?? 0) + $taskTime;
            $allTask['taskList'][] = [
                'task' => $task->getTodoId(),
                'level' => $task->getLevel(),
                'estimated' => $task->getEstimated(),
                'maxEstimated' => $taskTime
            ];
        }
        return $allTask;
    }

    public function totalCalculator($tasks)
    {
        $totalTask = 0;
        $totalTime = 0;
        $totalWeek = count($tasks['weekArray']);
        $minimumEstimated = self::WEEKLYWORKTIME;
        $weekArray = $tasks['weekArray'];
        foreach ($weekArray as $index => $week)
        {
            foreach ($week as $dev)
            {
                if($totalWeek === $index && $minimumEstimated > $dev['orgRemainingTime'])
                {
                    $minimumEstimated = $dev['orgRemainingTime'];
                }
                $userTime = 0;
                array_walk($dev['taskList'], function (&$task) use (&$userTime,&$totalTask){
                    $userTime += $task['estimated'];
                    $totalTask++;
                });
                $totalTime += $userTime;
            }
        }
        return [
            'totalTime' => $totalTime,
            'totalTask' => $totalTask,
            'totalWeek' => $totalWeek,
            'minimumEstimated' => self::WEEKLYWORKTIME - $minimumEstimated
        ];
    }

    private function descSortArray($array, $key)
    {
        usort($array, function ($a, $b) use ($key) {
            return $b[$key] <=> $a[$key];
        });
        return $array;
    }

    private function ascSortArray($array, $key)
    {
        usort($array, function ($a, $b) use ($key) {
            return $a[$key] <=> $b[$key];
        });
        return $array;
    }
}
