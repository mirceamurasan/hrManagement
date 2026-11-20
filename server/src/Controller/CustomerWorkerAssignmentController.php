<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CustomerWorkerAssignment;
use App\Form\CustomerWorkerAssignmentType;
use App\Repository\CustomerRepository;
use App\Repository\CustomerWorkerAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/customers/{customerId}/workers', name: 'customer_worker_', requirements: ['customerId' => '\\d+'])]
#[IsGranted('ROLE_PM')]
final class CustomerWorkerAssignmentController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(int $customerId, Request $request, CustomerRepository $customers, CustomerWorkerAssignmentRepository $assignments, EntityManagerInterface $em): Response
    {
        $customer = $customers->find($customerId);
        if ($customer === null || $customer->isTrashed()) { throw $this->createNotFoundException('Customer not found.'); }
        $assignment = new CustomerWorkerAssignment($customer);
        $form = $this->createForm(CustomerWorkerAssignmentType::class, $assignment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $assignments->findOneBy(['customer' => $customer, 'worker' => $assignment->getWorker()]);
            if ($existing !== null && !$existing->isTrashed()) {
                $this->addFlash('warning', 'This worker is already assigned to this customer.');
            } else {
                if ($existing !== null) {
                    $existing->setTrashed(false);
                } else {
                    $em->persist($assignment);
                }
                $em->flush();
                $this->addFlash('success', 'Worker assigned to customer.');
            }
            return $this->redirectToRoute('customer_worker_index', ['customerId' => $customerId]);
        }
        return $this->render('customer_worker/index.html.twig', [
            'customer' => $customer,
            'assignments' => $assignments->findActiveForCustomer($customer),
            'form' => $form,
        ]);
    }

    #[Route('/{id}/remove', name: 'remove', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function remove(int $customerId, int $id, Request $request, CustomerRepository $customers, CustomerWorkerAssignmentRepository $assignments, EntityManagerInterface $em): Response
    {
        $customer = $customers->find($customerId);
        $assignment = $assignments->find($id);
        if ($customer === null || $assignment === null || $assignment->getCustomer()->getId() !== $customerId) {
            throw $this->createNotFoundException('Worker assignment not found.');
        }
        if ($this->isCsrfTokenValid('remove-customer-worker-'.$id, (string) $request->request->get('_token'))) {
            $assignment->setTrashed(true);
            $em->flush();
        }
        return $this->redirectToRoute('customer_worker_index', ['customerId' => $customerId]);
    }
}
