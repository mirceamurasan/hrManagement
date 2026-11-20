<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserAccount;
use App\Form\UserAccountType;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration/accounts', name: 'admin_account_')]
#[IsGranted('ROLE_ADMIN')]
final class UserAccountController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserAccountRepository $accounts): Response
    {
        return $this->render('account/index.html.twig', ['accounts' => $accounts->findBy([], ['email' => 'ASC'])]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $account = new UserAccount('');
        $form = $this->createForm(UserAccountType::class, $account, ['password_required' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = (string) $form->get('plainPassword')->getData();
            if (mb_strlen($password) < 12) {
                $form->get('plainPassword')->addError(new FormError('Use at least 12 characters.'));
            } else {
                $account->setPassword($hasher->hashPassword($account, $password));
                $em->persist($account);
                $em->flush();
                $this->addFlash('success', 'Account created.');
                return $this->redirectToRoute('admin_account_index');
            }
        }
        return $this->render('account/form.html.twig', ['form' => $form, 'title' => 'New account']);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, UserAccountRepository $accounts, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $account = $accounts->find($id);
        if ($account === null) { throw $this->createNotFoundException('Account not found.'); }
        $form = $this->createForm(UserAccountType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = (string) $form->get('plainPassword')->getData();
            if ($password !== '' && mb_strlen($password) < 12) {
                $form->get('plainPassword')->addError(new FormError('Use at least 12 characters.'));
            } else {
                if ($password !== '') { $account->setPassword($hasher->hashPassword($account, $password)); }
                $em->flush();
                $this->addFlash('success', 'Account updated.');
                return $this->redirectToRoute('admin_account_index');
            }
        }
        return $this->render('account/form.html.twig', ['form' => $form, 'title' => 'Edit account']);
    }
}
