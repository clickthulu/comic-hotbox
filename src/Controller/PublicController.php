<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\CarouselImage;
use App\Entity\HotBox;
use App\Entity\Webring;
use App\Entity\WebringImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/hotbox.js', name: 'app_hotboxjavascript')]
    public function getJavascript(): Response
    {

        $customPath = file_exists(__DIR__ . "/../../custom/public/hotbox-widget.js.twig") ? "custom" : "templates";

        $env = $this->container->get('twig');
        $loader = $env->getLoader();
        $loader->addPath($customPath, 'custom');
        $env->setLoader($loader);

        $response = new Response();
        $response->headers->set("Content-type", "application/javascript");
        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $this->render('public/hotbox.js.twig',[], $response);

    }

    #[Route('/api/hotbox/{code}', name: 'app_hotboxcode')]
    public function getHotBox(EntityManagerInterface $entityManager, string $code): JsonResponse
    {
        /**
         * @var HotBox $hotbox
         */
        $hotbox = $entityManager->getRepository(HotBox::class)->findOneBy(['code' => $code]);
        $rotation = $hotbox->getCurrentRotation();

        $data = [];
        if (!empty($rotation)) {
            $image = $rotation->getComic()->getRandomImage($hotbox);
            $imageAlt = $image->getAlttext() ?? $rotation->getComic()->getDescription();
            $imageURL = $image->getUrl() ?? $rotation->getComic()->getUrl();

            $data = [
                'title' => $rotation->getComic()->getName(),
                'image' => $image->getPath(),
                'description' => $imageAlt,
                'url' => $imageURL
            ];
        }
        return new JsonResponse($data);

    }


    #[Route('/api/carousel/{code}', name: 'app_carouselcode')]
    public function getCarousel(EntityManagerInterface $entityManager, string $code): JsonResponse
    {
        /**
         * @var Carousel $carousel
         */
        $carousel = $entityManager->getRepository(Carousel::class)->findOneBy(['code' => $code]);

        $data = [
            'name' => $carousel->getName(),
            'width' => $carousel->getWidth(),
            'height' => $carousel->getHeight(),
            'transition' => $carousel->getDisplayType(),
            'delay' => $carousel->getDelay(),
            'code' => $code,
            'items' => []
        ];

        $cimages = $carousel->getCarouselImages();
        /**
         * @var CarouselImage $carouselImage
         */
        foreach ($cimages as $carouselImage) {
            if (!$carouselImage->isActive()) {
                continue;
            }
            $data['items'][] = [
                'url' => $carouselImage->getComic()->getUrl(),
                'image' => $carouselImage->getPath(),
                'name' => $carouselImage->getComic()->getName(),

            ];
        }

        return new JsonResponse($data);

    }

    #[Route('/api/webring/{code}/{comiccode}', name: 'app_webringcode')]
    public function getWebring(EntityManagerInterface $entityManager, string $code, string $comiccode): JsonResponse
    {
        /**
         * @var Webring $webring
         */
        $webring = $entityManager->getRepository(Webring::class)->findOneBy(['code' => $code]);
        $images = $webring->getWebringImages()->toArray();
        $numImages = $webring->getNumberImages();
        $rightSide = floor($numImages/2);

        $webringParts = [];

        $seenCount = 0;
        $maxSeen = count($images);
        $rightFound = false;
        $rightCount = 0;

        while($rightSide+1 > count($webringParts)) {
            /**
             * @var WebringImage $image
             */
            $image = array_shift($images);
            $seenCount++;
            if (!$image->isActive()) {
                continue;
            } elseif ($seenCount >= $maxSeen) {
                break;
            } elseif ($image->getComic()->getCode() === $comiccode) {
                $webringParts[] = $image;
                $rightFound = true;
            } elseif ($rightFound === true && $rightCount < $rightSide) {
                $webringParts[] = $image;
                $rightCount++;
            } else {
                $images[] = $image;
            }
        }
        $images = array_reverse($images);
        while($numImages > count($webringParts)) {
            $image = array_shift($images);
            if (!$image->isActive()) {
                continue;
            }
            array_unshift($webringParts, $image);
            if (empty($images)) {
                break;
            }
        }

        $data = [
            'name' => $webring->getName(),
            'width' => $webring->getRingWidth(),
            'height' => $webring->getRingHeight(),
            'nav' => $webring->getNavigationWidth(),
            'code' => $code,
            'previous' => null,
            'next' => null,
            'items' => []
        ];
        $previous = null;
        $currentSeen = false;
        /**
         * @var WebringImage $image
         */
        foreach ($webringParts as $image) {
            $current = $image->getComic()->getCode() === $comiccode;
            if ($currentSeen) {
                $data['next'] = $image->getComic()->getUrl();
                $currentSeen = false;
            }
            $data['items'][] = [
                'url' => $image->getComic()->getUrl(),
                'image' => $image->getPath(),
                'name' => $image->getComic()->getName(),
                'current' => $current
            ];
            if ($current) {
                $data['previous'] = $previous->getComic()->getUrl();
                $currentSeen = true;
            }
            $previous = $image;
        }

        $foo = 'bar';

        return new JsonResponse($data);

    }

    #[Route('/api/info', name: 'app_hotboxinfo')]
    public function getInfo(EntityManagerInterface $entityManager): JsonResponse
    {
        /**
         * @var HotBox $hotbox
         */
        $hotboxes = $entityManager->getRepository(HotBox::class)->findAll();
        $data = [];
        /**
         * @var HotBox $hotbox
         */
        foreach ($hotboxes as $hotbox) {
            $data[] = [
                'name' => $hotbox->getName(),
                'size' => "{$hotbox->getImageWidth()} x {$hotbox->getImageHeight()}",
                'code' => $hotbox->getCode()
            ];
        }
        return new JsonResponse($data);
    }

}