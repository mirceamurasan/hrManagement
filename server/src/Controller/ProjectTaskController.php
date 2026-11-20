<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProjectTask;
use App\Entity\UserAccount;
use App\Form\ProjectTaskType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectTaskRepository;
use App\Repository\TaskStateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projects/{projectId}/tasks', name: 'project_task_', requirements: ['projectId' => '\\d+'])]
#[IsGranted('ROLE_FORECAST_USER')]
final class ProjectTaskController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PM')]
    public function new(
        int $projectId,
        Request $request,
        ProjectRepository $projects,
        ProjectTaskRepository $tasks,
        TaskStateRepository $states,
        EntityManagerInterface $entityManager,
    ): Response {
        $project = $projects->find($projectId);
        if ($project === null || $project->isTrashed()) {
            throw $this->createNotFoundException('Project not found.');
        }

        $user = $this->getUser();
        if (!$user instanceof UserAccount || $user->getId() === null) {
            throw $this->createAccessDeniedException();
        }

        $defaultState = $states->findDefaultActive() ?? $states->findOneBy(['isTrashed' => false]);
        if ($defaultState === null) {
            $this->addFlash('warning', 'Add an active task state before creating tasks.');

            return $this->redirectToRoute('admin_task_state_index');
        }

        $start = new \DateTimeImmutable('today');
        $task = new ProjectTask($project, $defaultState, $user->getId(), '', $start, $start);
        $form = $this->createForm(ProjectTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setSortOrder($tasks->nextOrderForProject($project));
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash('success', 'Task created.');

            return $this->redirectToRoute('project_show', ['id' => $projectId]);
        }

        return $this->render('project_task/new.html.twig', ['project' => $project, 'form' => $form]);
    }

    #[Route('/{taskId}/edit', name: 'edit', requirements: ['taskId' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PM')]
    public function edit(
        int $projectId,
        int $taskId,
        Request $request,
        ProjectTaskRepository $tasks,
        EntityManagerInterface $entityManager,
    ): Response {
        $task = $tasks->find($taskId);
        if ($task === null || $task->getProject()->getId() !== $projectId || $task->isTrashed()) {
            throw $this->createNotFoundException('Task not found.');
        }

        $form = $this->createForm(ProjectTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Task updated.');

            return $this->redirectToRoute('project_show', ['id' => $projectId]);
        }

        return $this->render('project_task/edit.html.twig', ['task' => $task, 'form' => $form]);
    }

    #[Route('/{taskId}/archive', name: 'archive', requirements: ['taskId' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_PM')]
    public function archive(
        int $projectId,
        int $taskId,
        Request $request,
        ProjectTaskRepository $tasks,
        EntityManagerInterface $entityManager,
    ): Response {
        $task = $tasks->find($taskId);
        if ($task === null || $task->getProject()->getId() !== $projectId) {
            throw $this->createNotFoundException('Task not found.');
        }

        if ($this->isCsrfTokenValid('archive-task-'.$taskId, (string) $request->request->get('_token'))) {
            $task->archive();
            $entityManager->flush();
            $this->addFlash('success', 'Task archived. Its allocations were archived too.');
        }

        return $this->redirectToRoute('project_show', ['id' => $projectId]);
    }
}
