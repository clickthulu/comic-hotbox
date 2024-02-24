<?php

namespace App\Service;

use App\Entity\HotBox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsService
{
    private string $serverurl;
    private string $servertitle;
    private string $serverowner;

    private int $imagelimit = 4;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->serverurl = $parameterBag->get('serverURL');
        $this->servertitle = $parameterBag->get('serverName');
        $this->serverowner = $parameterBag->get('serverOwner');

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

    /**
     * @return array|bool|float|int|string|\UnitEnum|null
     */
    public function getServertitle(): \UnitEnum|float|int|bool|array|string|null
    {
        return $this->servertitle;
    }

    /**
     * @return array|bool|float|int|string|\UnitEnum|null
     */
    public function getServerowner(): \UnitEnum|float|int|bool|array|string|null
    {
        return $this->serverowner;
    }


}