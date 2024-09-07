<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Comic;
use App\Entity\HotBox;
use App\Entity\Webring;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $hotboxes = $entityManager->getRepository(HotBox::class)->findAll();
        $carousels = $entityManager->getRepository(Carousel::class)->findAll();
        $webrings = $entityManager->getRepository(Webring::class)->findAll();
        $comics = $entityManager->getRepository(Comic::class)->findBy(['user' => $this->getUser()]);
        return $this->render('dashboard/index.html.twig', [
            'hotboxes' => $hotboxes,
            'carousels' => $carousels,
            'webrings' => $webrings,
            'comics' => $comics
        ]);
    }

    #[Route('/credits', name: 'app_credits')]
    public function credits(): Response
    {
        return $this->render('dashboard/credits.html.twig', [
        ]);
    }
}
