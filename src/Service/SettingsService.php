<?php

namespace App\Service;

use App\Entity\Comic;
use App\Entity\HotBox;
use App\Entity\Image;
use App\Entity\Settings;
use App\Entity\SettingsCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsService
{
    private SettingsCollection $settingsCollection;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->settingsCollection = new SettingsCollection();

        $dbsettings = $entityManager->getRepository(Settings::class)->findAll();
        foreach ($dbsettings as $dbsetting) {
            $this->settingsCollection->addItem($dbsetting);
        }

        $owner = $parameterBag->get('serverOwner');
        $ownerSettings = new Settings();
        $ownerSettings->setType('string')->setSetting('server_owner')->setValue($owner);
        $this->settingsCollection->addItem($ownerSettings);

        $unapprovedComics = $entityManager->getRepository(Comic::class)->findBy(['approved' => false]);
        $comics = new Settings();
        $comics->setType('int')->setSetting('comics_pending')->setValue(count($unapprovedComics));
        $this->settingsCollection->addItem($comics);

        $unapprovedImages = $entityManager->getRepository(Image::class)->findImagesForActiveComics(['com.approved' => true, 'img.approved' => false]);
        $images = new Settings();
        $images->setType('int')->setSetting('images_pending')->setValue(count($unapprovedImages));
        $this->settingsCollection->addItem($images);



    }

    public function get(string $key)
    {
        return $this->settingsCollection->get($key);
    }

    public function set(Settings $item): self
    {
        $this->settingsCollection->addItem($item);
        return $this;
    }
}