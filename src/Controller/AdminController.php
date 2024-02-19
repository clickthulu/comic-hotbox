<?php

namespace App\Controller;

use App\Entity\InviteUser;
use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use App\Exceptions\ClickthuluException;
use App\Exceptions\HotBoxException;
use App\Form\InviteUsersType;
use App\Helpers\SettingsHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

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
        } catch (\Exception $e){
            $err = new FormError($e->getMessage());
            $form->addError($err);
        }

        return $this->render('admin/inviteusers.html.twig', [
            'inviteForm' => $form->createView()
        ]);
    }



}