<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProjectTask;
use App\Entity\TaskAllocation;
use App\Entity\UserAccount;
use App\Form\TaskAllocationType;
use App\Repository\ProjectTaskRepository;
use App\Repository\TaskAllocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projects/{projectId}/tasks/{taskId}/allocations', name: 'task_allocation_', requirements: ['projectId' => '\\d+', 'taskId' => '\\d+'])]
#[IsGranted('ROLE_PM')]
final class TaskAllocationController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(int $projectId, int $taskId, Request $request, ProjectTaskRepository $tasks, EntityManagerInterface $entityManager): Response
    {
        $task = $this->loadTask($projectId, $taskId, $tasks);
        $user = $this->getUser();
        if (!$user instanceof UserAccount || $user->getId() === null) {
            throw $this->createAccessDeniedException();
        }

        $allocation = new TaskAllocation($task, $this->getDefaultJob($entityManager), 1, $user->getId());
        $form = $this->createForm(TaskAllocationType::class, $allocation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($allocation);
            $entityManager->flush();
            $this->addFlash('success', 'Allocation added.');

            return $this->redirectToRoute('project_show', ['id' => $projectId]);
        }

        return $this->render('task_allocation/new.html.twig', ['task' => $task, 'form' => $form]);
    }

    #[Route('/{allocationId}/archive', name: 'archive', requirements: ['allocationId' => '\\d+'], methods: ['POST'])]
    public function archive(int $projectId, int $taskId, int $allocationId, Request $request, ProjectTaskRepository $tasks, TaskAllocationRepository $allocations, EntityManagerInterface $entityManager): Response
    {
        $task = $this->loadTask($projectId, $taskId, $tasks);
        $allocation = $allocations->find($allocationId);
        if ($allocation === null || $allocation->getTask()->getId() !== $task->getId()) {
            throw $this->createNotFoundException('Allocation not found.');
        }

        if ($this->isCsrfTokenValid('archive-allocation-'.$allocationId, (string) $request->request->get('_token'))) {
            $allocation->setTrashed(true);
            $entityManager->flush();
            $this->addFlash('success', 'Allocation archived.');
        }

        return $this->redirectToRoute('project_show', ['id' => $projectId]);
    }

    private function loadTask(int $projectId, int $taskId, ProjectTaskRepository $tasks): ProjectTask
    {
        $task = $tasks->find($taskId);
        if ($task === null || $task->getProject()->getId() !== $projectId || $task->isTrashed()) {
            throw $this->createNotFoundException('Task not found.');
        }

        return $task;
    }

    private function getDefaultJob(EntityManagerInterface $entityManager): \App\Entity\Job
    {
        $job = $entityManager->getRepository(\App\Entity\Job::class)->findOneBy(['isTrashed' => false]);
        if ($job === null) {
            throw $this->createNotFoundException('Create a job before adding allocations.');
        }

        return $job;
    }
}
