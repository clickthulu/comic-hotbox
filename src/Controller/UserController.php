<?php

namespace App\Controller;

use App\Entity\InviteUser;
use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use App\Exceptions\HotBoxException;
use App\Form\EditUserType;
use App\Form\InviteUsersType;
use App\Service\SettingsService;
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
        return new RedirectResponse($this->generateUrl('app_manageuser'));
    }


    #[Route('/admin/user/edit/{id}', name: 'app_edituser')]
    public function editUser(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var User $user
         */
        $user = $entityManager->getRepository(User::class)->find($id);
        $current = $this->getUser();
        $isOwner = $user->getId() === $current->getId() && in_array(RoleEnumeration::ROLE_OWNER, $current->getRoles());

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $roles = $form->get('roles')->getData();
                $roles = array_merge($roles, [RoleEnumeration::ROLE_CREATOR]);
                if ($isOwner) {
                    $roles = array_merge($roles, [RoleEnumeration::ROLE_CREATOR]);
                }
                $user->setName($form->get('name')->getData())
                    ->setEmail($form->get('email')->getData())
                    ->setRoles($roles);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('info', "{$user->getName()} has been updated.");
                return new RedirectResponse($this->generateUrl('app_manageuser'));
            } catch (Exception $e){
                $err = new FormError($e->getMessage());
                $this->addFlash('error', "{$e->getMessage()}");
                $form->addError($err);
            }
        }

        return $this->render('user/edituser.html.twig', [
            'userform' => $form->createView()
        ]);
    }


    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/user/invite', name: 'app_admininviteusers')]
    public function inviteUsers(MailerInterface $mailer, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, SettingsService $settings, Request $request): Response
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

                    $servername = $settings->get('server_name')->getValue() ?? 'HotBox';

                    $message = (new Email())
                        ->from($emailfrom)
                        ->to($email)
                        ->subject("You have been invited to join {$servername}")
                        ->html($this->render("registration/userinvite_email.html.twig", [ "user" => $user,  "reg" => $regcode ])->getContent());
                    $mailer->send($message);

                    $this->addFlash('info', "{$email} invited");

                }
                $entityManager->flush();
                return new RedirectResponse($this->generateUrl('app_manageuser'));
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

}