<?php

namespace App\Controller;

use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use App\Exceptions\ClickthuluException;
use App\Exceptions\HotBoxException;
use App\Form\RegistrationFormType;
use App\Helpers\SettingsHelper;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager, ?string $email): Response
    {
        $email = $request->query->get('email');
        $code = $request->query->get('code');

        $user = new User();

        if (!empty($email) && !empty($code)) {

            /**
             * @var RegistrationCode $regcode
             */
            $regcode = $entityManager->getRepository(RegistrationCode::class)->findOneBy(['code' => $code]);
            if ($regcode->getExpireson() < new \DateTime()) {
                throw new HotBoxException("This registration code has expired");
            } elseif (  $regcode->isActivated() === false || $regcode->getEmail() !== $email) {
                throw new HotBoxException("This registration code has is no longer available");
            }

        }
        $user->setEmail($email);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setName($form->get('name')->getData());


            $user->setRoles([RoleEnumeration::ROLE_CREATOR]);
            $regcode->setActivated(true);
            $entityManager->persist($user);
            $entityManager->persist($regcode);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/acceptinvite/{code}', name: 'app_verify_invite')]
    public function verifyUserInvite(string $code, Request $request, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var RegistrationCode $regcode
         */
        $regcode = $entityManager->getRepository(RegistrationCode::class)->findOneBy(['code' => $code]);
        if ($regcode->isActivated() === false && $regcode->getExpireson() >= new \DateTime()) {
            $regcode->setActivated(true);
            $entityManager->persist($regcode);
            $entityManager->flush();
            return new RedirectResponse($this->generateUrl('app_register', ['email' => $regcode->getEmail(), 'code' => $code]));
        }

        return $this->render('error.html.twig', ['message' => "We're sorry.  The invite code you used is either invalid or expired."]);
    }
}
