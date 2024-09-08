<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\CarouselImage;
use App\Entity\HotBox;
use App\Entity\Webring;
use App\Entity\WebringImage;
use App\Exceptions\HotBoxException;
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

    #[Route('/api/webring/{code}/{comiccode?}', name: 'app_webringcode')]
    public function getWebring(EntityManagerInterface $entityManager, string $code, ?string $comiccode = null): JsonResponse
    {
        /**
         * @var Webring $webring
         */
        $webring = $entityManager->getRepository(Webring::class)->findOneBy(['code' => $code]);
        $startimages = $webring->getWebringImages()->toArray();
        $images = [];
        $newOrd = 0;
        foreach ($startimages as $image) {
            if ($image->isActive()) {
                $image->setOrdinal($newOrd);
                $newOrd++;
                $images[] = $image;
            }
        }


        if (empty($comiccode)) {
            $tempImages = array_merge($images, []);
            shuffle($tempImages);
            foreach ($tempImages as $tempImage) {
                if ($tempImage->isActive()) {
                    $comiccode = $tempImage->getComic()->getCode();
                    break;
                }
            }
        }

        $numImages = $webring->getNumberImages();
        $rightSide = floor($numImages/2);
        $leftSide = $numImages - $rightSide - 1;
        // Find the mid-point
        $visibleImages = [];
        $midPointOrdinal = null;
        /**
         * @var WebringImage $image
         */
        foreach ($images as $image) {
            if ($image->getComic()->getCode() === $comiccode) {
                $visibleImages[] = $image;
                $midPointOrdinal = $image->getOrdinal();
            }
        }

        for ($ord = $midPointOrdinal+1; $ord <= $midPointOrdinal + $rightSide; $ord++) {
            $visibleImages[] = $this->getImageFromOrdinalDelta($images, $ord);
        }


        for ($ord = $midPointOrdinal-1; $ord >= $midPointOrdinal - $leftSide; $ord--) {
            array_unshift($visibleImages, $this->getImageFromOrdinalDelta($images, $ord));
        }



        $data = [
            'name' => $webring->getName(),
            'width' => $webring->getRingWidth(),
            'height' => $webring->getRingHeight(),
            'nav' => $webring->getNavigationWidth(),
            'navprev' => $this->getWebringNavImage($webring, 'Previous'),
            'navnext' => $this->getWebringNavImage($webring, 'Next'),
            'code' => $code,
            'previous' => $this->getImageFromOrdinalDelta($images, $midPointOrdinal-1)->getComic()->getUrl(),
            'next' => $this->getImageFromOrdinalDelta($images, $midPointOrdinal+1)->getComic()->getUrl(),
            'items' => []
        ];

        /**
         * @var WebringImage $image
         */
        foreach ($visibleImages as $image) {
            $data['items'][] = [
                'url' => $image->getComic()->getUrl(),
                'image' => $image->getPath(),
                'name' => $image->getComic()->getName(),
            ];
        }

        return new JsonResponse($data);

    }

    #[Route('/api/info/{comiccode?}', name: 'app_hotboxinfo')]
    public function getInfo(EntityManagerInterface $entityManager, ?string $comiccode): JsonResponse
    {
        $hotboxes = $entityManager->getRepository(HotBox::class)->findAll();
        $carousels = $entityManager->getRepository(Carousel::class)->findAll();
        $webrings = $entityManager->getRepository(Webring::class)->findAll();

        $data = [
            'hotbox' => [],
            'carousel' => [],
            'webring' => []
        ];
        /**
         * @var HotBox $hotbox
         */
        foreach ($hotboxes as $hotbox) {
            $data['hotbox'][] = [
                'name' => $hotbox->getName(),
                'size' => "{$hotbox->getImageWidth()} x {$hotbox->getImageHeight()}",
                'code' => $hotbox->getCode()
            ];
        }
        /**
         * @var Carousel $carousel
         */
        foreach ($carousels as $carousel) {
            $data['carousel'][] = [
                'name' => $carousel->getName(),
                'size' => "{$carousel->getWidth()} x {$carousel->getHeight()}",
                'code' => $carousel->getCode()
            ];
        }


        /**
         * @var Webring $webring
         */
        foreach ($webrings as $webring) {
            $data['webring'][] = [
                'name' => $webring->getName(),
                'size' => "{$webring->getRingWidth()} x {$webring->getRingHeight()}",
                'code' => $webring->getCode(),
                'comiccode' => $comiccode
            ];
        }
        return new JsonResponse($data);
    }

    protected function getWebringNavImage(Webring $webring, string $dir = 'Previous'): string
    {
        $image = match($dir) {
            'Previous' => $webring->getNavigationLeft(),
            'Next' => $webring->getNavigationRight()
        };

        if (!empty($image)) {
            return "<img src='{$image}' alt='{$dir}'/>";
        }


        $w = (float)$webring->getNavigationWidth();
        $h = (float)$webring->getRingHeight();

        // Previous : Default
        $w0 = 2;
        $w1 = $w-5;
        $w2 = $w;
        $h0 = $h/2;
        $h1 = 0;
        $h2 = $h;

        if ($dir === 'Next') {
            $w0 = $w - 2;
            $w1 = 5;
            $w2 = 0;
        }

        $out = "<svg width='{$w}' height='{$h}' xmlns='http://www.w3.org/2000/svg'>";
        $out .= "<polygon points='{$w0},{$h0} {$w1},{$h1} {$w2},{$h1} {$w2},{$h2} {$w1},{$h2} {$w0},{$h0}' style='stroke:white;stroke-width:3;fill:#333333;' />";
        $out .= "</svg>";

        return $out;
    }

    protected function getImageFromOrdinalDelta(array $images, $ordinal) {
        $maxOrdinal = 0;

        /**
         * @var WebringImage $image
         */
        foreach ($images as $image) {
            if ($image->getOrdinal() > $maxOrdinal) {
                $maxOrdinal = $image->getOrdinal();
            }
        }

        if ($ordinal > $maxOrdinal) {
            $ordinal -= ($maxOrdinal+1);
        }
        if ($ordinal < 0) {
            $ordinal += $maxOrdinal+1;
        }

        foreach ($images as $image) {
            if ($image->getOrdinal() === $ordinal) {
                return $image;
            }
        }
        throw new HotBoxException("Could not find image with ordinal {$ordinal}");
    }

}