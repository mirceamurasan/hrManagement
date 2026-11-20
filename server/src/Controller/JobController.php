<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/jobs', name: 'admin_job_')]
#[IsGranted('ROLE_ADMIN')]
final class JobController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(JobRepository $jobs): Response
    {
        return $this->render('catalog/job_index.html.twig', ['items' => $jobs->findBy([], ['label' => 'ASC']), 'name' => 'Jobs', 'edit_route' => 'admin_job_edit', 'create_route' => 'admin_job_new']);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $job = new Job('');
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($job);
            $entityManager->flush();
            $this->addFlash('success', 'Job created.');

            return $this->redirectToRoute('admin_job_index');
        }

        return $this->render('catalog/job_form.html.twig', ['form' => $form, 'title' => 'New job']);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, JobRepository $jobs, EntityManagerInterface $entityManager): Response
    {
        $job = $jobs->find($id);
        if ($job === null) { throw $this->createNotFoundException('Job not found.'); }
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Job updated.');

            return $this->redirectToRoute('admin_job_index');
        }

        return $this->render('catalog/job_form.html.twig', ['form' => $form, 'title' => 'Edit job']);
    }
}
