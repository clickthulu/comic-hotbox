<?php

namespace App\Controller;

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
            $data = [
                'title' => $rotation->getComic()->getName(),
                'image' => $rotation->getComic()->getRandomImage($hotbox)->getPath(),
                'description' => $rotation->getComic()->getDescription(),
                'url' => $rotation->getComic()->getUrl()
            ];
        }
        return new JsonResponse($data);

    }


}