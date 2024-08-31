<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Enumerations\RoleEnumeration;
use App\Form\CarouselFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

}