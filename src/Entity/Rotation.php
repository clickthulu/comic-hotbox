<?php

namespace App\Entity;

use App\Enumerations\RotationFrequencyEnumeration;
use App\Repository\RotationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RotationRepository::class)]
class Rotation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rotations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HotBox $hotbox = null;

    #[ORM\ManyToOne(inversedBy: 'rotations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comic $comic = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expire = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $ordinal = 0;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHotbox(): ?HotBox
    {
        return $this->hotbox;
    }

    public function setHotbox(?HotBox $hotbox): static
    {
        $this->hotbox = $hotbox;

        return $this;
    }

    public function getComic(): ?Comic
    {
        return $this->comic;
    }

    public function setComic(?Comic $comic): static
    {
        $this->comic = $comic;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;
        return $this;
    }

    public function calculateNextStart(): static
    {
        $frequency = $this->hotbox->getRotationFrequency();
        $hotboxRotations = $this->hotbox->getRotations();
        $lastDate = RotationFrequencyEnumeration::getStarting($frequency);

        if ($hotboxRotations->count() === 0) {
            $this->start = $lastDate;
            return $this;
        }

        /**
         * @var Rotation $rotation
         */
        foreach ($hotboxRotations as $rotation) {
            if ($rotation->getStart() > $lastDate) {
                $lastDate = $rotation->getStart();
            }
        }

        $this->start = RotationFrequencyEnumeration::getNextStart($frequency, $lastDate);
        return $this;
    }

    function calculateExpire(): static
    {
        $this->expire = RotationFrequencyEnumeration::getNextStart($this->hotbox->getRotationFrequency(), $this->start);
        return $this;
    }

    public function getExpire(): ?\DateTimeInterface
    {
        return $this->expire;
    }

    public function setExpire(\DateTimeInterface $expire): static
    {
        $this->expire = $expire;

        return $this;
    }

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(?int $ordinal): static
    {
        $this->ordinal = $ordinal;

        return $this;
    }

}
