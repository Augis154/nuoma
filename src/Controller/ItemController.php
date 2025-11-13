<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Review;
use App\Form\NewItemFormType;
use App\Form\ReviewFormType;
use App\Repository\ItemRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ItemController extends AbstractController
{
    #[Route('/', name: 'app_items')]
    public function index(Request $request, ItemRepository $itemRepository): Response
    {
        // $items = $itemRepository->findAll();

        if ($searchTerm = $request->query->get('q')) {
            $items = $itemRepository->search($searchTerm);
        } else {
            $items = $itemRepository->findAll();
        }

        return $this->render('item/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/objects/{id}', name: 'app_item')]
    public function item(Item $item, Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $reviewForm = $this->createForm(ReviewFormType::class, $review);
        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            $review->setItem($item);
            $review->setCreatedAt(new \DateTimeImmutable());

            $user = $this->getUser();
            $review->setCreatedBy($user);

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_item', ['id' => $item->getId()]);
        }

        return $this->render('item/item.html.twig', [
            'item' => $item,
            'reviewForm' => $reviewForm,
        ]);
    }

    #[IsGranted('ROLE_LENDER')]
    #[Route('/object/new', name: 'app_item_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $item = new Item();
        $form = $this->createForm(NewItemFormType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $image) {
                    $imageFileName = $fileUploader->upload($image);
                    $item->addImage($imageFileName);
                }
            }

            $item->setCreatedBy($this->getUser());
            $item->setCreatedAt(new \DateTimeImmutable());
            $item->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute('app_item', ['id' => $item->getId()]);
        }

        return $this->render('item/new.html.twig', [
            'newItemForm' => $form,
        ]);
    }

    #[Route('/object/{id}', name: 'app_item_delete', methods: ['DELETE'])]
    public function delete(Request $request, Item $item, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('ROLE_LENDER');

        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            // Delete associated images
            foreach ($item->getImages() as $image) {
                $fileUploader->delete($image);
            }

            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_items');
    }
}
