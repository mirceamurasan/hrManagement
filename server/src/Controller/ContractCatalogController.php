<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContractType;
use App\Entity\Employer;
use App\Form\ContractTypeType;
use App\Form\EmployerType;
use App\Repository\ContractTypeRepository;
use App\Repository\EmployerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ContractCatalogController extends AbstractController
{
    #[Route('/administration/employers', name: 'admin_employer_index', methods: ['GET'])]
    public function employers(EmployerRepository $repo): Response { return $this->render('contract_catalog/employers.html.twig', ['items' => $repo->findBy([], ['name' => 'ASC'])]); }

    #[Route('/administration/employers/new', name: 'admin_employer_new', methods: ['GET', 'POST'])]
    public function newEmployer(Request $request, EntityManagerInterface $em): Response
    {
        $item = new Employer('');
        $form = $this->createForm(EmployerType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $em->persist($item); $em->flush(); return $this->redirectToRoute('admin_employer_index'); }
        return $this->render('contract_catalog/form.html.twig', ['form' => $form, 'title' => 'New employer', 'back' => 'admin_employer_index']);
    }

    #[Route('/administration/contract-types', name: 'admin_contract_type_index', methods: ['GET'])]
    public function types(ContractTypeRepository $repo): Response { return $this->render('contract_catalog/types.html.twig', ['items' => $repo->findBy([], ['name' => 'ASC'])]); }

    #[Route('/administration/contract-types/new', name: 'admin_contract_type_new', methods: ['GET', 'POST'])]
    public function newType(Request $request, EntityManagerInterface $em): Response
    {
        $item = new ContractType('');
        $form = $this->createForm(ContractTypeType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $em->persist($item); $em->flush(); return $this->redirectToRoute('admin_contract_type_index'); }
        return $this->render('contract_catalog/form.html.twig', ['form' => $form, 'title' => 'New contract type', 'back' => 'admin_contract_type_index']);
    }
}
