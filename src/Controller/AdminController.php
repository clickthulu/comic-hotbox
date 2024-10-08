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
use App\Form\EditUserType;
use App\Form\InviteUsersType;
use App\Form\SettingsType;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    #[Route('/admin/approve/comics', name: 'app_adminapprovecomic')]
    public function approveComics(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $comics = $entityManager->getRepository(Comic::class)->findAll();

        return $this->render('admin/comiclist.html.twig', [
            'comics' => $comics
        ]);
    }

    #[Route('/admin/approve/images', name: 'app_adminapproveimages')]
    public function approveImages(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $images = $entityManager->getRepository(Image::class)->findImagesForActiveComics(['com.approved' => true], ['approved' => 'ASC']);

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
        $approved = !$comic->isApproved();
        $comic->setApproved($approved);
        if (!$approved) {
            foreach ($comic->getImages() as $image) {
                $image->setApproved(false);
                $entityManager->persist($image);
            }
        }


        $entityManager->persist($comic);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl("app_adminapprovecomic"));
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
        return new RedirectResponse($this->generateUrl('app_manageuser'));
    }

    #[Route('/admin/image/toggle/{id}', name: 'app_toggleimage')]
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
        return new RedirectResponse($this->generateUrl("app_adminapproveimages"));
    }


    #[Route('/admin/settings', name: 'app_settings')]
    public function manageSettings(EntityManagerInterface $entityManager, Request $request): Response
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

    #[Route('/admin/media', name: 'app_managemedia')]
    public function manageMedia(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $uploadDir = __DIR__ . "/../../storage/_admin";
        $storageDir = "/storage/_admin";
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir);
        }

        $files = glob("{$uploadDir}/*");

        $filelist = [];

        foreach ($files as $file) {
            $base = basename($file);
            $path = "{$storageDir}/{$base}";
            $upload = filemtime($file);
            $filelist[] = [
                'file' => $base,
                'path' => $path,
                'upload' => $upload
            ];
        }

        return $this->render(
            'admin/managemedia.html.twig',
            [
                'files' => $filelist
            ]
        );
    }

    #[Route('/admin/media/upload', name: 'app_uploadmedia')]
    public function uploadMedia(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $uploadDir = __DIR__ . "/../../storage/_admin";
        $storageDir = "/storage/_admin";
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir);
        }
        $files = array_pop($_FILES);
        if (empty($files)) {
            throw new FileNotFoundException("No file was uploaded.");
        }


        $path = "{$uploadDir}/{$files['name']}";
        move_uploaded_file($files['tmp_name'], $path);
        return new RedirectResponse($this->generateUrl('app_managemedia'));
    }

    #[Route('/admin/media/delete/{file}', name: 'app_deletemedia')]
    public function deleteMedia(string $file): Response
    {
        $uploadDir = __DIR__ . "/../../storage/_admin";

        if (file_exists("{$uploadDir}/{$file}")) {
            @unlink("{$uploadDir}/{$file}");
        }
        return new RedirectResponse($this->generateUrl('app_managemedia'));
    }
}