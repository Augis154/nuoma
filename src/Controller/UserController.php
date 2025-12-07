<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Flag;
use App\Entity\Item;
use App\Form\EditUserFormType;
use App\Repository\FlagRepository;
use App\Repository\ItemRepository;
use App\Repository\LeaseRepository;
use App\Service\FileUploader;
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

        $category = $request->query->get('category');
        if ($searchTerm = $request->query->get('q')) {
            $items = $itemRepository->search($searchTerm, $category, $user);
        } else {
            $items = $itemRepository->findByCreatedBy($user, $category);
        }

        return $this->render('user/items.html.twig', [
            'user' => $user,
            'items' => $items,
        ]);
    }

    #[Route('/user/{id}/leases', name: 'app_user_leases')]
    public function leases(Request $request, User $user, ItemRepository $itemRepository, LeaseRepository $leaseRepository): Response
    {
        $items = $itemRepository->findByLessee($user);

        // if ($searchTerm = $request->query->get('q')) {
        //     $items = $itemRepository->search($searchTerm, $user);
        // } else {
        //     $items = $itemRepository->findByCreatedBy($user);
        // }

        return $this->render('user/leases.html.twig', [
            'user' => $user,
            'items' => $items,
        ]);
    }

    #[IsGranted('ROLE_LENDER')]
    #[Route('/flags', name: 'app_flags')]
    public function flags(Request $request, FlagRepository $flagRepository): Response
    {
        $flags = $flagRepository->findAll();

        return $this->render('user/flags.html.twig', [
            'flags' => $flags,
        ]);
    }

    #[IsGranted('ROLE_MODERATOR')]
    #[Route('/flags/delete', name: 'app_flag_delete_all', methods: ['POST'])]
    public function deleteFlags(Request $request, FlagRepository $flagRepository, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        foreach ($flagRepository->findAll() as $flag) {
            $this->deleteFlag($request, $flag, $entityManager, $fileUploader);
        }
        return $this->redirectToRoute('app_flags');
    }

    #[IsGranted('ROLE_MODERATOR')]
    #[Route('/flag/{id}', name: 'app_flag_delete', methods: ['POST'])]
    public function deleteFlag(Request $request, Flag $flag, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        if ($flag) {
            if ($flag->getReview()) {
                $entityManager->remove($flag->getReview());
            }
            else {
                $image = $flag->getImage();
                $entityManager->getRepository(Item::class)->deleteImage($image);
                $fileUploader->delete($image);
            }
            $entityManager->remove($flag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_flags');
    }
}
