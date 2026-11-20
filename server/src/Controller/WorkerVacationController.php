<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\WorkerVacation;
use App\Form\WorkerVacationType;
use App\Repository\ContractPeriodRepository;
use App\Repository\WorkerVacationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
final class WorkerVacationController extends AbstractController
{
    #[Route('/vacations', name: 'vacation_index', methods: ['GET'])]
    public function index(WorkerVacationRepository $vacations, ContractPeriodRepository $periods): Response
    {
        return $this->render('vacation/index.html.twig', ['vacations' => $vacations->findActive(), 'periods' => $periods->findActive()]);
    }

    #[Route('/periods/{periodId}/vacations/new', name: 'vacation_new', requirements: ['periodId' => '\\d+'], methods: ['GET', 'POST'])]
    public function new(int $periodId, Request $request, ContractPeriodRepository $periods, EntityManagerInterface $em): Response
    {
        $period = $periods->find($periodId);
        if ($period === null || $period->isTrashed()) { throw $this->createNotFoundException('Contract period not found.'); }
        $today = new DateTimeImmutable('today');
        $vacation = new WorkerVacation($period, $today, $today);
        $form = $this->createForm(WorkerVacationType::class, $vacation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($vacation);
            $em->flush();
            $this->addFlash('success', 'Vacation recorded.');
            return $this->redirectToRoute('admin_vacation_index');
        }
        return $this->render('vacation/new.html.twig', ['period' => $period, 'form' => $form]);
    }
}
