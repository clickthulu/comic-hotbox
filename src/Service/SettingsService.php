<?php

namespace App\Service;

use App\Entity\HotBox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsService
{
    private string $serverurl;
    private int $imagelimit = 4;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->serverurl = $parameterBag->get('serverURL');
        try {
            $this->imagelimit = $parameterBag->get('imageLimit');
        } catch (ParameterNotFoundException) {
        }
    }


    public function getServerURL(): string
    {
        return $this->serverurl;
    }

    public function getImageLimit(): int
    {
        return $this->imagelimit;
    }
}