<?php
declare(strict_types=1);
namespace App\Controller;
use App\Entity\Team;
use App\Entity\WorkLocation;
use App\Form\TeamType;
use App\Form\WorkLocationType;
use App\Repository\TeamRepository;
use App\Repository\WorkLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ADMIN')]
final class TeamStructureController extends AbstractController
{
    #[Route('/administration/teams', name: 'admin_team_index', methods: ['GET'])]
    public function teams(TeamRepository $repo): Response { return $this->render('team/teams.html.twig', ['items' => $repo->findBy([], ['name' => 'ASC'])]); }
    #[Route('/administration/teams/new', name: 'admin_team_new', methods: ['GET', 'POST'])]
    public function newTeam(Request $request, EntityManagerInterface $em): Response
    {
        $item = new Team('');
        $form = $this->createForm(TeamType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $em->persist($item); $em->flush(); return $this->redirectToRoute('admin_team_index'); }
        return $this->render('team/form.html.twig', ['form' => $form, 'title' => 'New team', 'back' => 'admin_team_index']);
    }
    #[Route('/administration/locations', name: 'admin_location_index', methods: ['GET'])]
    public function locations(WorkLocationRepository $repo): Response { return $this->render('team/locations.html.twig', ['items' => $repo->findBy([], ['name' => 'ASC'])]); }
    #[Route('/administration/locations/new', name: 'admin_location_new', methods: ['GET', 'POST'])]
    public function newLocation(Request $request, EntityManagerInterface $em): Response
    {
        $item = new WorkLocation('');
        $form = $this->createForm(WorkLocationType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $em->persist($item); $em->flush(); return $this->redirectToRoute('admin_location_index'); }
        return $this->render('team/form.html.twig', ['form' => $form, 'title' => 'New location', 'back' => 'admin_location_index']);
    }
}
