<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ImportRunRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/import-runs', name: 'admin_import_run_')]
#[IsGranted('ROLE_ADMIN')]
final class ImportRunController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ImportRunRepository $runs): Response
    {
        return $this->render('import_run/index.html.twig', ['runs' => $runs->findBy([], ['importedAt' => 'DESC'], 100)]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(int $id, ImportRunRepository $runs): Response
    {
        $run = $runs->find($id);
        if ($run === null) {
            throw $this->createNotFoundException('Import record not found.');
        }

        return $this->render('import_run/show.html.twig', ['run' => $run]);
    }
}
