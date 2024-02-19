<?php

namespace App\Controller;

use App\Entity\HotBox;
use App\Entity\Rotation;
use App\Enumerations\RoleEnumeration;
use App\Form\HotBoxCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HotBoxController extends AbstractController
{
    #[Route('/hotbox/create', name: 'app_createhotbox')]
    #[Route('/hotbox/edit/{id}', name: 'app_edithotbox')]
    public function create(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $hotbox = new HotBox();
        if (!empty($id)) {
            $hotbox = $entityManager->getRepository(HotBox::class)->find($id);
        }
        $form = $this->createForm(HotBoxCreateFormType::class, $hotbox);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            $code = $form->get('code')->getData();
            $active = $form->get('active')->getData();
            $comics = $form->get('comics')->getData();

            $hotbox
                ->setName($name)
                ->setCode($code)
                ->setActive($active)
            ;

            foreach ($comics as $comic) {
                $rotation = new Rotation();
                $rotation
                    ->setComic($comic)
                    ->setHotbox($hotbox)
                    ->calculateNextStart()
                    ->calculateExpire();

                $hotbox->addRotation($rotation);
            }

            $entityManager->persist($hotbox);
            $entityManager->flush();
            return new RedirectResponse("/");
        }



        return $this->render('hotbox/create.html.twig', [
            'hotboxform' => $form->createView()
        ]);



    }
}