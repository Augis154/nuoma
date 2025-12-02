<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Lease;
use App\Entity\Review;
use App\Form\LeaseFormType;
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
use App\Enum\ItemStatus;
use App\Enum\ItemCategory;

final class ItemController extends AbstractController
{
    #[Route('/', name: 'app_items')]
    public function index(Request $request, ItemRepository $itemRepository): Response
    {
        // $items = $itemRepository->findAll();
        $category = $request->query->get('category');
        $searchTerm = $request->query->get('q');

        if ($searchTerm) {
            $items = $itemRepository->search($searchTerm, $category);
        } else {
            $items = $itemRepository->findAll($category);
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

        switch ($item->getCategory()) {
            case ItemCategory::WORK:
                $categoryName = 'Darbo įrankiai';
                break;
            case ItemCategory::TABLE:
                $categoryName = 'Stalo įrankiai';
                break;
            case ItemCategory::ARTS:
                $categoryName = 'Meno įrankiai';
                break;
            case ItemCategory::AGRO:
                $categoryName = 'Žemės ūkio įrankiai';
                break;
            case ItemCategory::LEASURE:
                $categoryName = 'Laisvalaikio įrankiai';
                break;
            case ItemCategory::OTHER:
                $categoryName = 'Kiti įrankiai';
                break;
            default:
                $categoryName = 'Nežinoma kategorija';
        }

        return $this->render('item/item.html.twig', [
            'item' => $item,
            'reviewForm' => $reviewForm,
            'categoryName' => $categoryName,
        ]);
    }

    #[Route('/objects/{id}/lease', name: 'app_item_lease')]
    public function lease(Item $item, Request $request, EntityManagerInterface $entityManager): Response
    {
        $lease = new Lease();
        $leaseForm = $this->createForm(LeaseFormType::class, $lease);
        $leaseForm->handleRequest($request);

        if ($leaseForm->isSubmitted() && $leaseForm->isValid()) {
            $item->setStatus(ItemStatus::LEASED);
            
            $lease->setItem($item);
            $lease->setLessee($this->getUser());
            
            $lease->setReturned(false);
            $lease->setCreatedAt(new \DateTimeImmutable());
            $lease->setUpdatedAt(new \DateTimeImmutable());

            $lease->setLeasedFrom($leaseForm->get('from')->getData());
            $lease->setLeasedTo($leaseForm->get('to')->getData());

            $entityManager->persist($lease);
            $entityManager->flush();

            return $this->redirectToRoute('app_item', ['id' => $item->getId()]);
        }

        return $this->render('item/lease.html.twig', [
            'item' => $item,
            'leaseForm' => $leaseForm,
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
            $item->setCreatedBy($this->getUser());
            $item->setStatus(ItemStatus::AVAILABLE);
            $item->setCreatedAt(new \DateTimeImmutable());
            $item->setUpdatedAt(new \DateTimeImmutable());

            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $image) {
                    $imageFileName = $fileUploader->upload($image);
                    $item->addImage($imageFileName);
                }
            }

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

    #[Route('/object/{id}', name: 'app_item_set_returned', methods: ['POST'])]
    public function setReturned(Request $request, Item $item, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('ROLE_LENDER');

        if ($this->isCsrfTokenValid('set_returned' . $item->getId(), $request->request->get('_token'))) {
            $item->setStatus(ItemStatus::AVAILABLE);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_objects', ['id' => $this->getUser()->getUserIdentifier()]);
    }
}
