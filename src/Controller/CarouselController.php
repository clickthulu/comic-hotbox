<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\CarouselImage;
use App\Enumerations\RoleEnumeration;
use App\Form\CarouselFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarouselController extends AbstractController
{

    #[Route('/carousel/create', name: 'app_createcarousel')]
    #[Route('/carousel/edit/{id}', name: 'app_editcarousel')]
    public function create(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $carousel = new Carousel();
        $update = false;

        if (!empty($id)) {
            $carousel = $entityManager->getRepository(Carousel::class)->find($id);
            $update = true;
        }
        $form = $this->createForm(CarouselFormType::class, $carousel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $checkCar = $entityManager->getRepository(Carousel::class)->findBy(['name' => $form->get('name')->getData()]);
            if (empty($id) && !empty($checkCar)) {
                // This hotbox already exists
                $carousel = $checkCar;
            }

            $name = $form->get('name')->getData();
            $code = $form->get('code')->getData();
            $active = $form->get('active')->getData();
            $wid = $form->get('width')->getData();
            $hei = $form->get('height')->getData();

            $carousel
                ->setName($name)
                ->setCode($code)
                ->setActive($active)
                ->setWidth($wid)
                ->setHeight($hei)
            ;
            $entityManager->persist($carousel);
            $entityManager->flush();
            $this->addFlash('info', "Carousel " . ($update ? 'updated' : 'created'));
            return new RedirectResponse($this->generateUrl('app_editcarousel', ['id' => $carousel->getId()]));

        }
        return $this->render('carousel/create.html.twig', [
            'carouselform' => $form->createView(),
            'carousel' => $carousel
        ]);
    }

    #[Route('/carousel/delete/{id}', name: 'app_deletecarousel')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $carousel = $entityManager->getRepository(Carousel::class)->find($id);
        $entityManager->remove($carousel);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }

    #[Route('/carousel/order/{id}', name: 'app_updateordercarousel')]
    public function updateOrder(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $sortableItems = $request->request->all('items');
        foreach ($sortableItems as $ordinal => $cimageid) {
            /**
             * @var CarouselImage $cimage
             */
            $cimage = $entityManager->getRepository(CarouselImage::class)->find((int)$cimageid);
            $cimage->setOrdinal($ordinal);
            $entityManager->persist($cimage);
        }
        $entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }

    #[Route('/carousel/activate/{id}/{cid}', name: 'app_togglecarouselimage')]
    public function toggleImage(EntityManagerInterface $entityManager, int $id, int $cid): RedirectResponse
    {
        /**
         * @var CarouselImage $carouselImage
         */
        $carouselImage = $entityManager->getRepository(CarouselImage::class)->find($cid);
        $carouselImage->setActive(!$carouselImage->isActive());
        $entityManager->persist($carouselImage);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl('app_editcarousel', ['id' => $id]));
    }

}