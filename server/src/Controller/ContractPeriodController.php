<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContractPeriod;
use App\Form\ContractPeriodType;
use App\Repository\ContractPeriodRepository;
use App\Repository\EmploymentContractRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/contracts/{contractId}/periods', name: 'admin_contract_period_', requirements: ['contractId' => '\\d+'])]
#[IsGranted('ROLE_ADMIN')]
final class ContractPeriodController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(int $contractId, Request $request, EmploymentContractRepository $contracts, ContractPeriodRepository $periods, EntityManagerInterface $em): Response
    {
        $contract = $contracts->find($contractId);
        if ($contract === null || $contract->isTrashed()) { throw $this->createNotFoundException('Contract not found.'); }
        $period = new ContractPeriod($contract, new DateTimeImmutable('today'));
        $form = $this->createForm(ContractPeriodType::class, $period);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($period);
            $em->flush();
            $this->addFlash('success', 'Contract period created.');

            return $this->redirectToRoute('admin_contract_index');
        }
        return $this->render('contract_period/new.html.twig', ['contract' => $contract, 'periods' => $periods->findActiveByContract($contract), 'form' => $form]);
    }
}
