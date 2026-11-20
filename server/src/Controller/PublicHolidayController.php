<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PublicHoliday;
use App\Form\PublicHolidayType;
use App\Repository\PublicHolidayRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/daysoffsglobal', name: 'admin_public_holiday_')]
#[IsGranted('ROLE_ADMIN')]
final class PublicHolidayController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PublicHolidayRepository $holidays): Response
    {
        return $this->render('public_holiday/index.html.twig', [
            'holidays' => $holidays->findBy([], ['date' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $holiday = new PublicHoliday(new DateTimeImmutable(), '');
        $form = $this->createForm(PublicHolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($holiday);
            $entityManager->flush();
            $this->addFlash('success', 'Public holiday created.');

            return $this->redirectToRoute('admin_public_holiday_index');
        }

        return $this->render('public_holiday/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, PublicHolidayRepository $holidays, EntityManagerInterface $entityManager): Response
    {
        $holiday = $holidays->find($id);
        if ($holiday === null) {
            throw $this->createNotFoundException('Public holiday not found.');
        }

        $form = $this->createForm(PublicHolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Public holiday updated.');

            return $this->redirectToRoute('admin_public_holiday_index');
        }

        return $this->render('public_holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(int $id, Request $request, PublicHolidayRepository $holidays, EntityManagerInterface $entityManager): Response
    {
        $holiday = $holidays->find($id);
        if ($holiday === null) {
            throw $this->createNotFoundException('Public holiday not found.');
        }

        if ($this->isCsrfTokenValid('delete-public-holiday-'.$id, (string) $request->request->get('_token'))) {
            $entityManager->remove($holiday);
            $entityManager->flush();
            $this->addFlash('success', 'Public holiday deleted.');
        }

        return $this->redirectToRoute('admin_public_holiday_index');
    }
}
