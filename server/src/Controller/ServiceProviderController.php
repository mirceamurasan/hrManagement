<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ServiceProvider;
use App\Form\ServiceProviderType;
use App\Repository\ServiceProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/providers', name: 'admin_provider_')]
#[IsGranted('ROLE_ADMIN')]
final class ServiceProviderController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ServiceProviderRepository $providers): Response
    {
        return $this->render('catalog/provider_index.html.twig', ['items' => $providers->findBy([], ['name' => 'ASC']), 'name' => 'Service providers', 'edit_route' => 'admin_provider_edit', 'create_route' => 'admin_provider_new']);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $provider = new ServiceProvider('');
        $form = $this->createForm(ServiceProviderType::class, $provider);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($provider);
            $entityManager->flush();
            $this->addFlash('success', 'Provider created.');

            return $this->redirectToRoute('admin_provider_index');
        }

        return $this->render('catalog/provider_form.html.twig', ['form' => $form, 'title' => 'New service provider']);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, ServiceProviderRepository $providers, EntityManagerInterface $entityManager): Response
    {
        $provider = $providers->find($id);
        if ($provider === null) { throw $this->createNotFoundException('Provider not found.'); }
        $form = $this->createForm(ServiceProviderType::class, $provider);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Provider updated.');

            return $this->redirectToRoute('admin_provider_index');
        }

        return $this->render('catalog/provider_form.html.twig', ['form' => $form, 'title' => 'Edit service provider']);
    }
}
