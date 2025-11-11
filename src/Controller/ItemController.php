<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\NewItemFormType;
use App\Repository\ItemRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ItemController extends AbstractController
{
    #[Route('/objects', name: 'app_items')]
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
    public function item(Item $item): Response
    {
        return $this->render('item/item.html.twig', [
            'item' => $item,
        ]);
    }

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

            // $item->setImages($form->get('images')->getData());
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
}
