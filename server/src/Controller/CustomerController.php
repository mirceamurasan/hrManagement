<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\UserAccount;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/customers', name: 'customer_')]
final class CustomerController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted('ROLE_PM')]
    public function index(CustomerRepository $customers): Response
    {
        return $this->render('customer/index.html.twig', ['customers' => $customers->findActive()]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserAccount || $user->getId() === null) {
            throw $this->createAccessDeniedException();
        }

        $customer = new Customer('', $user->getId());
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customer);
            $entityManager->flush();
            $this->addFlash('success', 'Customer created.');

            return $this->redirectToRoute('customer_index');
        }

        return $this->render('customer/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(int $id, Request $request, CustomerRepository $customers, EntityManagerInterface $entityManager): Response
    {
        $customer = $customers->find($id);
        if ($customer === null) {
            throw $this->createNotFoundException('Customer not found.');
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Customer updated.');

            return $this->redirectToRoute('customer_index');
        }

        return $this->render('customer/edit.html.twig', ['customer' => $customer, 'form' => $form]);
    }
}
