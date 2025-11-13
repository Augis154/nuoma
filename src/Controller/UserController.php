<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserFormType;
use App\Repository\ItemRepository;
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

            if ($this->getUser() !== $user) {
                return $this->redirectToRoute('app_user', ['id' => $this->getUser()->getUserIdentifier()]);
            }
        }

        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            if ($editUserForm->has('roles')) {
                $role = $editUserForm->get('roles')->getData();

                if ($role) {
                    $user->setRoles(array($role));
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'editUserForm' => $editUserForm,
        ]);
    }

    #[Route('/user/{id}/objects', name: 'app_user_objects')]
    public function objects(Request $request, User $user, ItemRepository $itemRepository): Response
    {
        if ($searchTerm = $request->query->get('q')) {
            $items = $itemRepository->search($searchTerm, $user);
        } else {
            $items = $itemRepository->findByCreatedBy($user);
        }

        return $this->render('user/items.html.twig', [
            'user' => $user,
            'items' => $items,
        ]);
    }
}
