<?php

namespace App\Service;

use App\Entity\HotBox;
use Doctrine\ORM\EntityManagerInterface;

class HotBoxService
{
    protected array $hotboxes;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->hotboxes = $entityManager->getRepository(HotBox::class)->findBy(['active' => true]);
    }

    public function getHotboxes()
    {
        return $this->hotboxes;
    }

}