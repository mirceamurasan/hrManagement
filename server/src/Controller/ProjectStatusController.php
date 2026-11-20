<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProjectStatus;
use App\Form\ProjectStatusType;
use App\Repository\ProjectStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/projectstatuses', name: 'admin_project_status_')]
#[IsGranted('ROLE_ADMIN')]
final class ProjectStatusController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ProjectStatusRepository $statuses): Response
    {
        return $this->render('project_status/index.html.twig', [
            'statuses' => $statuses->findBy([], ['label' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = new ProjectStatus('');
        $form = $this->createForm(ProjectStatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($status);
            $entityManager->flush();
            $this->addFlash('success', 'Project status created.');

            return $this->redirectToRoute('admin_project_status_index');
        }

        return $this->render('project_status/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, ProjectStatusRepository $statuses, EntityManagerInterface $entityManager): Response
    {
        $status = $statuses->find($id);
        if ($status === null) {
            throw $this->createNotFoundException('Project status not found.');
        }

        $form = $this->createForm(ProjectStatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Project status updated.');

            return $this->redirectToRoute('admin_project_status_index');
        }

        return $this->render('project_status/edit.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }
}
