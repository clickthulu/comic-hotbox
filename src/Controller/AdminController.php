<?php

namespace App\Controller;

use App\Entity\Comic;
use App\Entity\Image;
use App\Entity\InviteUser;
use App\Entity\RegistrationCode;
use App\Entity\Settings;
use App\Entity\SettingsCollection;
use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use App\Exceptions\HotBoxException;
use App\Form\InviteUsersType;
use App\Form\SettingsType;
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
            $this->addFlash('error', "{$e->getMessage()}");
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


    #[Route('/admin/comic/toggle/{id}', name: 'app_togglecomicapprove')]
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



    #[Route('/admin/user/toggle/{id}', name: 'app_toggleuser')]
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

    #[Route('/admin/image/toggle/{id}', name: 'app_toggleuser')]
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

    #[Route('/admin/user/delete/{id}', name: 'app_deleteuser')]
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


    #[Route('/admin/user/edit/{id}', name: 'app_edituser')]
    public function editUser(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var User $usr
         */
        $usr = $entityManager->getRepository(User::class)->find($id);

        return new RedirectResponse("/user");
    }



    #[Route('/settings', name: 'app_settings')]
    public function getSettings(EntityManagerInterface $entityManager, Request $request): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!in_array("ROLE_OWNER", $user->getRoles()) && !in_array("ROLE_ADMIN", $user->getRoles())) {
            $this->addFlash('error', 'You do not have permission to perform this action');
            return new RedirectResponse($this->generateUrl("app_profile"), 403);
        }

        $items = $entityManager->getRepository(\App\Entity\Settings::class)->findAll();
        $settingsCollection = new SettingsCollection();
        /**
         * @var Settings $item
         */
        foreach ($items as $item) {
            $settingsCollection->addItem($item);
        }
        $form = $this->createForm(SettingsType::class, $settingsCollection);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var Settings $item
                 */
                foreach ($settingsCollection->getItems() as $item) {
                    $entityManager->persist($item);
                }
                $entityManager->flush();
                $this->addFlash('info', 'Settings have been updated');
                return new RedirectResponse($this->generateUrl('app_settings'));
            }
        } catch (\Exception $e){
            $err = new FormError($e->getMessage());
            $form->addError($err);
        }



        return $this->render(
            'admin/settings.html.twig',
            [
                'settingsForm' => $form->createView()
            ]
        );
    }

}