<?php

namespace App;

use App\Entity\Rotation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();
        date_default_timezone_set($this->getContainer()->getParameter('timezone'));
        /**
         * @var EntityManagerInterface $entityManager
         */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $rotations = $entityManager->getRepository(Rotation::class)->findAll();
        $now = new \DateTime();
        $saveFlag = false;
        /**
         * @var Rotation $rotation
         */
        foreach ($rotations as $rotation) {
            if ($now > $rotation->getExpire()) {
                $rotation->calculateNextStart()->calculateExpire();
                $entityManager->persist($rotation);
                $saveFlag = true;
            }
        }
        if ($saveFlag) {
            $entityManager->flush();
        }
    }
}
