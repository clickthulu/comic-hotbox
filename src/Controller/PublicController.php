<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\CarouselImage;
use App\Entity\HotBox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/hotbox.js', name: 'app_hotboxjavascript')]
    public function getJavascript()
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


    #[Route('/api/carousel/{code}', name: 'app_hotboxcode')]
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
            $data['items'][] = [
                'url' => $carouselImage->getComic()->getUrl(),
                'image' => $carouselImage->getPath(),
                'name' => $carouselImage->getComic()->getName(),

            ];
        }

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