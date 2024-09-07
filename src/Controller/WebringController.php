<?php

namespace App\Controller;

use App\Entity\Webring;
use App\Entity\WebringImage;
use App\Enumerations\RoleEnumeration;
use App\Form\WebringFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebringController extends AbstractController
{
    #[Route('/webring/create', name: 'app_createwebring')]
    #[Route('/webring/edit/{id}', name: 'app_editwebring')]
    public function create(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        $webring = new Webring();
        $update = false;
        if (!empty($id)) {
            $webring = $entityManager->getRepository(Webring::class)->find($id);
            $update = true;
        }

        $form = $this->createForm(WebringFormType::class, $webring);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $checkWR = $entityManager->getRepository(Webring::class)->findBy(['name' => $form->get('name')->getData()]);
            if (empty($id) && !empty($checkWR)) {
                // This hotbox already exists
                $webring = $checkWR;
            }
            $name = $form->get('name')->getData();
            $code = $form->get('code')->getData();
            $active = $form->get('active')->getData();
            $wid = $form->get('ringWidth')->getData();
            $hei = $form->get('ringHeight')->getData();
            $nav = $form->get('navigationWidth')->getData();
            $num = $form->get('numberImages')->getData();

            $webring
                ->setName($name)
                ->setCode($code)
                ->setActive($active)
                ->setRingWidth($wid)
                ->setRingHeight($hei)
                ->setNavigationWidth($nav)
                ->setNumberImages($num)
            ;
            $entityManager->persist($webring);
            $entityManager->flush();
            $this->addFlash('info', "Webring " . ($update ? 'updated' : 'created'));
            return new RedirectResponse($this->generateUrl('app_editwebring', ['id' => $webring->getId()]));


        }

        $images = $webring->getWebringImages();
        /**
         * @var WebringImage $image
         */
        $image = $images[0];
        $comicCode = $image->getComic()->getCode();

        return $this->render('webring/create.html.twig', [
            'webringform' => $form->createView(),
            'webring' => $webring,
            'comicCode' => $comicCode
        ]);
    }


    #[Route('/webring/delete/{id}', name: 'app_deletewebring')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $webring = $entityManager->getRepository(Webring::class)->find($id);
        $entityManager->remove($webring);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }

    #[Route('/webring/order/{id}', name: 'app_updateorderwebring')]
    public function updateOrder(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $sortableItems = $request->request->all('items');
        foreach ($sortableItems as $ordinal => $wimageid) {
            /**
             * @var WebringImage $wimage
             */
            $wimage = $entityManager->getRepository(WebringImage::class)->find((int)$wimageid);
            $wimage->setOrdinal($ordinal);
            $entityManager->persist($wimage);
        }
        $entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }


    #[Route('/webring/activate/{id}/{wid}', name: 'app_togglewebringimage')]
    public function toggleImage(EntityManagerInterface $entityManager, int $id, int $wid): RedirectResponse
    {
        /**
         * @var WebringImage $webringImage
         */
        $webringImage = $entityManager->getRepository(WebringImage::class)->find($wid);
        $webringImage->setActive(!$webringImage->isActive());
        $entityManager->persist($webringImage);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl('app_editwebring', ['id' => $id]));
    }

}