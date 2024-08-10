<?php

namespace App\Controller;

use App\Entity\Comic;
use App\Entity\HotBox;
use App\Entity\Image;
use App\Entity\User;
use App\Exceptions\HotBoxException;
use App\Form\ComicFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComicController extends AbstractController
{

    #[Route('/comic/create', name: 'app_newcomic')]
    #[Route('/comic/edit/{id}', name: 'app_editcomic')]
    public function create(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!empty($id)) {
            /**
             * @var Comic $comic
             */
            $comic = $entityManager->getRepository(Comic::class)->find($id);
            if ($user->getId() !== $comic->getUser()->getId()) {
                throw new HotBoxException("This comic does not belong to the logged in user");
            }
        } else {
            $comic = new Comic();
            $comic->setUser($user);
        }
        $hotboxes = $entityManager->getRepository(HotBox::class)->findAll();
        $comic->setImageHotboxMatch($hotboxes);

        $form = $this->createForm(ComicFormType::class, $comic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('Name')->getData();
            $url = $form->get('url')->getData();
            $desc = $form->get('description')->getData();
            $comic->setName($name)->setUrl($url)->setDescription($desc)->setApproved(false);
            $entityManager->persist($comic);
            $entityManager->flush();
            $id = $comic->getId();

            return new RedirectResponse($this->generateUrl('app_editcomic', ['id' => $id]));
        }


        return $this->render('comic/create.html.twig', [
            'comicform' => $form->createView(),
            'images' => $comic->getImages(),
        ]);


    }


    #[Route('/comic/delete/{id}', name: 'app_deletecomic')]
    public function delete(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (empty($id)) {
            throw new HotBoxException("Cannot delete comic");
        }
        /**
         * @var Comic $comic
         */
        $comic = $entityManager->getRepository(Comic::class)->find($id);
        if ($user->getId() !== $comic->getUser()->getId()) {
            throw new HotBoxException("This comic does not belong to the logged in user");
        }

        $entityManager->remove($comic);
        $entityManager->flush();

        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }

    #[Route('/comic/uploadimage/{comicid}/{imageid?}', name: 'app_uploadimage')]
    public function uploadimage(Request $request, EntityManagerInterface $entityManager, int $comicid, ?int $imageid = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        /**
         * @var Comic $comic
         */
        $comic = $entityManager->getRepository(Comic::class)->find($comicid);
        if ($user->getId() !== $comic->getUser()->getId()) {
            throw new HotBoxException("This comic does not belong to the logged in user");
        }

        if (!empty($imageid)) {
            /**
             * @var Image $origImage
             */
            $origImage = $entityManager->getRepository(Image::class)->find($imageid);
            if ($origImage->getComic()->getId() !== $comic->getId()) {
                throw new HotBoxException("Image in question does not belong to comic");
            }
            $entityManager->remove($origImage);
            $entityManager->flush();
            // Delete the current image
        }

        $uploadDir = __DIR__ . "/../../storage/{$user->getEmail()}";
        $storageDir = "/storage/{$user->getEmail()}";
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir);
        }

        $files = array_pop($_FILES);
        if (empty($files)) {
            throw new FileNotFoundException("No file was uploaded.");
        }


        $path = "{$uploadDir}/{$files['name']}";
        move_uploaded_file($files['tmp_name'], $path);

        list($imageWidth, $imageHeight,,) = getimagesize($path);
        $image = new Image();
        $image
            ->setComic($comic)
            ->setActive(true)
            ->setPath("{$storageDir}/{$files['name']}")
            ->setWidth($imageWidth)
            ->setHeight($imageHeight)
        ;
        $entityManager->persist($image);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl('app_editcomic', ['id' => $comicid]));
    }

    #[Route('/comic/image/delete/{comicid}/{imageid}', name: 'app_deleteimage')]
    public function deleteImage(Request $request, EntityManagerInterface $entityManager, int $comicid, ?int $imageid = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $comic = $entityManager->getRepository(Comic::class)->find($comicid);
        if ($user->getId() !== $comic->getUser()->getId()) {
            throw new HotBoxException("This comic does not belong to the logged in user");
        }

        /**
         * @var Image $image
         */
        $image = $entityManager->getRepository(Image::class)->find($imageid);
        $entityManager->remove($image);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl('app_editcomic', ['id' => $comicid]));
    }


    #[Route('/comic/image/activate/{comicid}/{imageid}', name: 'app_activateimage')]
    public function activateimage(Request $request, EntityManagerInterface $entityManager, int $comicid, int $imageid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->changeImageActiveFlag($entityManager, $comicid, $imageid, true);
    }


    #[Route('/comic/image/deactivate/{comicid}/{imageid}', name: 'app_deactivateimage')]
    public function deactivateimage(Request $request, EntityManagerInterface $entityManager, int $comicid, int $imageid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $return = $this->changeImageActiveFlag($entityManager, $comicid, $imageid, false);
        /**
         * @var Comic $comic
         */
        $comic = $entityManager->getRepository(Comic::class)->find($comicid);
        $hotboxes = $entityManager->getRepository(HotBox::class)->findAll();

        foreach ($hotboxes as $hotbox) {
            if (!$comic->imageSizeMatch($hotbox)) {
                // Remove comic from hotbox rotation;
                $comic->clearRotationsFromHotBox($hotbox);
            }

        }



        return $return;
    }


    protected function changeImageActiveFlag(EntityManagerInterface $entityManager, int $comicid, int $imageid, bool $active = true)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $comic = $entityManager->getRepository(Comic::class)->find($comicid);
        if ($user->getId() !== $comic->getUser()->getId()) {
            throw new HotBoxException("This comic does not belong to the logged in user");
        }

        /**
         * @var Image $image
         */
        $image = $entityManager->getRepository(Image::class)->find($imageid);
        $image->setActive($active);
        $entityManager->persist($image);
        $entityManager->flush();
        return new RedirectResponse($this->generateUrl('app_editcomic', ['id' => $comicid]));
    }
}