<?php

namespace App\Controller;

use App\Entity\Comic;
use App\Entity\Image;
use App\Entity\InviteUser;
use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use App\Exceptions\HotBoxException;
use App\Form\InviteUsersType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/user/invite', name: 'app_admininviteusers')]
    public function inviteUsers(MailerInterface $mailer, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $invite = new InviteUser();

        $form = $this->createForm(InviteUsersType::class, $invite);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $emails = $data->getUserArray();

                foreach ($emails as $email) {
                    $regcode = new RegistrationCode();
                    $regcode->setEmail($email)->generate();
                    $entityManager->persist($regcode);

                    $emailfrom = $parameterBag->get('emailFrom');
                    if (empty($emailfrom)) {
                        throw new HotBoxException("Email From address is not configured.");
                    }


                    $message = (new Email())
                        ->from($emailfrom)
                        ->to($email)
                        ->subject("You have been invited to join HotBox")
                        ->html($this->render("registration/userinvite_email.html.twig", [ "user" => $user,  "reg" => $regcode ])->getContent());
                    $mailer->send($message);

                    $this->addFlash('info', "{$email} invited");

                }
                $entityManager->flush();
            }
        } catch (Exception $e){
            $err = new FormError($e->getMessage());
            $form->addError($err);
        }

        return $this->render('admin/inviteusers.html.twig', [
            'inviteForm' => $form->createView()
        ]);
    }

    #[Route('/approve/comics', name: 'app_adminapprovecomic')]
    public function approveComics(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $comics = $entityManager->getRepository(Comic::class)->findAll();

        return $this->render('admin/comiclist.html.twig', [
            'comics' => $comics
        ]);
    }

    #[Route('/approve/images', name: 'app_adminapproveimages')]
    public function approveImages(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $images = $entityManager->getRepository(Image::class)->findBy([], ['approved' => 'ASC']);

        return $this->render('admin/imagelist.html.twig', [
            'images' => $images
        ]);


    }


    #[Route('/admin/toggle/comic/{id}', name: 'app_togglecomic')]
    public function toggleComic(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var Comic
         */
        $comic = $entityManager->getRepository(Comic::class)->find($id);
        $comic->setApproved(!$comic->isApproved());
        $entityManager->persist($comic);
        $entityManager->flush();
        return new RedirectResponse("/approve/comics");
    }



    #[Route('/admin/toggle/user/{id}', name: 'app_toggleuser')]
    public function toggleUser(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var User $usr
         */
        $usr = $entityManager->getRepository(User::class)->find($id);
        $usr->setActive(!$usr->isActive());
        $entityManager->persist($usr);
        $entityManager->flush();
        return new RedirectResponse("/user");
    }

    #[Route('/admin/toggle/image/{id}', name: 'app_toggleuser')]
    public function toggleImage(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var Image $img
         */
        $img = $entityManager->getRepository(Image::class)->find($id);
        $img->setApproved(!$img->isApproved());
        $entityManager->persist($img);
        $entityManager->flush();
        return new RedirectResponse("/approve/images");
    }

    #[Route('/admin/delete/user/{id}', name: 'app_deleteuser')]
    public function deleteUser(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var User $usr
         */
        $usr = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($usr);
        $entityManager->flush();
        return new RedirectResponse("/user");
    }
}