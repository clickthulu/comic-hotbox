<?php

namespace App\Service;

use App\Entity\Comic;
use App\Entity\HotBox;
use Doctrine\ORM\EntityManagerInterface;

class HotBoxService
{
    protected array $hotboxes;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->hotboxes = $hotboxes = $entityManager->getRepository(HotBox::class)->findBy(['active' => true]);

        $comics = $entityManager->getRepository(Comic::class)->findBy(['active' => true, 'approved' => true]);
        /**
         * @var Comic $comic
         */
        foreach ($comics as $comic) {
            /**
             * @var HotBox $hotbox
             */
            if ($comic->isActive() && $comic->isApproved()) {
                foreach ($hotboxes as $hotbox) {
                    $comic->imageSizeMatch($hotbox);
                    if ($comic->isHotboxMatch()) {
                        $hotbox->incAvailable();
                    }

                    foreach ($comic->getRotations() as $rotation) {
                        if ($rotation->getHotbox()->getId() === $hotbox->getId()) {
                            $hotbox->incActive();
                        }
                    }
                }
            }
        }

    }

    public function getHotboxes()
    {
        return $this->hotboxes;
    }

}