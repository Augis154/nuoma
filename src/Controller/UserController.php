<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user')]
    public function index(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $editUserForm = $this->createForm(EditUserFormType::class, $user);

        if (!$this->isGranted('ROLE_ADMIN')) {
            // Non-admin users should not be able to edit roles
            $editUserForm->remove('roles');
        }

        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $role = $editUserForm->get('roles')->getData();

            if ($role) {
                $user->setRoles(array($role));
            }

            // $editUserForm->getData();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'editUserForm' => $editUserForm,
        ]);
    }
}
