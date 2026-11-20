<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Worker;
use App\Form\WorkerType;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/workers', name: 'admin_worker_')]
#[IsGranted('ROLE_ADMIN')]
final class WorkerController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(WorkerRepository $workers): Response
    {
        return $this->render('worker/index.html.twig', ['workers' => $workers->findBy([], ['name' => 'ASC'])]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $worker = new Worker('');
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($worker);
            $entityManager->flush();
            $this->addFlash('success', 'Worker profile created.');

            return $this->redirectToRoute('admin_worker_index');
        }

        return $this->render('worker/form.html.twig', ['form' => $form, 'title' => 'New worker']);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, WorkerRepository $workers, EntityManagerInterface $entityManager): Response
    {
        $worker = $workers->find($id);
        if ($worker === null) { throw $this->createNotFoundException('Worker not found.'); }
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Worker profile updated.');

            return $this->redirectToRoute('admin_worker_index');
        }

        return $this->render('worker/form.html.twig', ['form' => $form, 'title' => 'Edit worker']);
    }
}
