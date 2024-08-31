<?php

namespace App\Service;

use App\Entity\Carousel;
use Doctrine\ORM\EntityManagerInterface;

class CarouselService
{
    protected array $carousels;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->carousels = $carousels = $entityManager->getRepository(Carousel::class)->findBy(['active' => true]);
    }

    public function getCarousels(): array
    {
        return $this->carousels;
    }

}