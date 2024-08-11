<?php

namespace App\Controller;

use App\Entity\HotBox;
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


        return $this->render('dashboard/index.html.twig', [
            'hotboxes' => $hotboxes,

        ]);
    }

    #[Route('/credits', name: 'app_credits')]
    public function credits(): Response
    {
        return $this->render('dashboard/credits.html.twig', [
        ]);
    }
}
