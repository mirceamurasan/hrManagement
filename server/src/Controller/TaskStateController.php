<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TaskState;
use App\Form\TaskStateType;
use App\Repository\TaskStateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/task-states', name: 'admin_task_state_')]
#[IsGranted('ROLE_ADMIN')]
final class TaskStateController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(TaskStateRepository $states): Response
    {
        return $this->render('task_state/index.html.twig', ['states' => $states->findBy([], ['label' => 'ASC'])]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskStateRepository $states, EntityManagerInterface $entityManager): Response
    {
        $state = new TaskState('');
        $form = $this->createForm(TaskStateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->unsetOtherDefaults($states, $state);
            $entityManager->persist($state);
            $entityManager->flush();
            $this->addFlash('success', 'Task state created.');

            return $this->redirectToRoute('admin_task_state_index');
        }

        return $this->render('task_state/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, TaskStateRepository $states, EntityManagerInterface $entityManager): Response
    {
        $state = $states->find($id);
        if ($state === null) {
            throw $this->createNotFoundException('Task state not found.');
        }

        $form = $this->createForm(TaskStateType::class, $state);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->unsetOtherDefaults($states, $state);
            $entityManager->flush();
            $this->addFlash('success', 'Task state updated.');

            return $this->redirectToRoute('admin_task_state_index');
        }

        return $this->render('task_state/edit.html.twig', ['state' => $state, 'form' => $form]);
    }

    private function unsetOtherDefaults(TaskStateRepository $states, TaskState $selected): void
    {
        if (!$selected->isDefault()) {
            return;
        }

        foreach ($states->findBy(['isTrashed' => false]) as $state) {
            if ($state !== $selected && $state->isDefault()) {
                $state->setDefault(false);
            }
        }
    }
}
