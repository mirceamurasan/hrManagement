<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Entity\UserAccount;
use App\Form\ProjectType;
use App\Repository\CustomerRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectTaskRepository;
use App\Repository\ProjectKeyDateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projects', name: 'project_')]
#[IsGranted('ROLE_FORECAST_USER')]
final class ProjectController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ProjectRepository $projects): Response
    {
        return $this->render('project/index.html.twig', ['projects' => $projects->findActive()]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PM')]
    public function new(Request $request, CustomerRepository $customers, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserAccount || $user->getId() === null) {
            throw $this->createAccessDeniedException();
        }

        $availableCustomers = $customers->findActive();
        if ($availableCustomers === []) {
            $this->addFlash('warning', 'Create a customer before adding a project.');

            return $this->redirectToRoute('customer_index');
        }

        $project = new Project($availableCustomers[0], $user->getId());
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();
            $this->addFlash('success', 'Project created.');

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(int $id, ProjectRepository $projects, ProjectTaskRepository $tasks, ProjectKeyDateRepository $milestones): Response
    {
        $project = $projects->find($id);
        if ($project === null || $project->isTrashed()) {
            throw $this->createNotFoundException('Project not found.');
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tasks' => $tasks->findActiveByProject($project),
            'milestones' => $milestones->findByProject($project),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PM')]
    public function edit(int $id, Request $request, ProjectRepository $projects, EntityManagerInterface $entityManager): Response
    {
        $project = $projects->find($id);
        if ($project === null || $project->isTrashed()) {
            throw $this->createNotFoundException('Project not found.');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Project updated.');

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', ['project' => $project, 'form' => $form]);
    }
}
