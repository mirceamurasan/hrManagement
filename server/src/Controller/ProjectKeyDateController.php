<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProjectKeyDate;
use App\Form\ProjectKeyDateType;
use App\Repository\ProjectKeyDateRepository;
use App\Repository\ProjectRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projects/{projectId}/milestones', name: 'project_milestone_', requirements: ['projectId' => '\\d+'])]
#[IsGranted('ROLE_PM')]
final class ProjectKeyDateController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(int $projectId, Request $request, ProjectRepository $projects, EntityManagerInterface $em): Response
    {
        $project = $projects->find($projectId);
        if ($project === null || $project->isTrashed()) { throw $this->createNotFoundException('Project not found.'); }
        $milestone = new ProjectKeyDate($project, new DateTimeImmutable('today'), '');
        $form = $this->createForm(ProjectKeyDateType::class, $milestone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($milestone);
            $em->flush();
            return $this->redirectToRoute('project_show', ['id' => $projectId]);
        }
        return $this->render('project_key_date/new.html.twig', ['project' => $project, 'form' => $form]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(int $projectId, int $id, Request $request, ProjectKeyDateRepository $milestones, EntityManagerInterface $em): Response
    {
        $milestone = $milestones->find($id);
        if ($milestone === null || $milestone->getProject()->getId() !== $projectId) { throw $this->createNotFoundException('Milestone not found.'); }
        if ($this->isCsrfTokenValid('delete-milestone-'.$id, (string) $request->request->get('_token'))) {
            $em->remove($milestone);
            $em->flush();
        }
        return $this->redirectToRoute('project_show', ['id' => $projectId]);
    }
}
