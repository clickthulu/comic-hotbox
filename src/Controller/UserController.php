<?php

namespace App\Controller;

use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/user', name: 'app_manageuser')]
    public function manage(EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('user/manage.html.twig', ['users' => $users]);
    }

}