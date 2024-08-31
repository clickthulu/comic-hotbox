<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class WebringService
{
    protected array $webrings = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
//        $this->webrings = $webrings = $entityManager->getRepository(Webring::class)->findBy(['active' => true]);
    }

    public function getwebrings(): array
    {
        return $this->webrings;
    }

}