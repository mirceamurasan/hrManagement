<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\EmploymentContract;
use App\Entity\Worker;
use App\Form\EmploymentContractType as EmploymentContractForm;
use App\Repository\ContractTypeRepository;
use App\Repository\EmployerRepository;
use App\Repository\EmploymentContractRepository;
use App\Repository\JobRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/contracts', name: 'admin_contract_')]
#[IsGranted('ROLE_ADMIN')]
final class EmploymentContractController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EmploymentContractRepository $contracts): Response { return $this->render('contract/index.html.twig', ['contracts' => $contracts->findActive()]); }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, WorkerRepository $workers, ContractTypeRepository $types, EmployerRepository $employers, JobRepository $jobs, EntityManagerInterface $em): Response
    {
        $worker = $request->query->getInt('worker') ?: null;
        $worker = $worker ? $workers->find($worker) : $workers->findActive()[0] ?? null;
        if (!$worker instanceof Worker) { $this->addFlash('warning', 'Create a worker profile first.'); return $this->redirectToRoute('admin_worker_index'); }
        $type = $types->findOneBy(['isTrashed' => false]);
        $employer = $employers->findOneBy(['isTrashed' => false]);
        $job = $jobs->findOneBy(['isTrashed' => false]);
        if ($type === null || $employer === null || $job === null) {
            $this->addFlash('warning', 'Add an active contract type, employer and job before creating a contract.');
            return $this->redirectToRoute('admin_contract_type_index');
        }
        $contract = new EmploymentContract($worker, $type, $employer, $job);
        $form = $this->createForm(EmploymentContractForm::class, $contract);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $em->persist($contract); $em->flush(); return $this->redirectToRoute('admin_contract_index'); }
        return $this->render('contract/form.html.twig', ['form' => $form, 'title' => 'New contract']);
    }
}
