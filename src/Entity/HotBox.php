<?php

namespace App\Entity;

use App\Enumerations\RotationFrequencyEnumeration;
use App\Repository\HotBoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HotBoxRepository::class)]
class HotBox
{
    const CODELENGTH = 16;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $createdon = null;

    #[ORM\Column]
    private ?bool $active = true;

    #[ORM\OneToMany(mappedBy: 'hotbox', targetEntity: Rotation::class, orphanRemoval: true)]
    private Collection $rotations;

    #[ORM\Column]
    private ?string $rotationFrequency = null;

    #[ORM\Column]
    private ?int $imageWidth = 120;

    #[ORM\Column]
    private ?int $imageHeight = 600;

    public function __construct()
    {
        $this->createdon = new \DateTime();
        $this->code = $this->generateCode();
        $this->rotations = new ArrayCollection();
        $this->rotationFrequency = RotationFrequencyEnumeration::LENGTH_DAY;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }


    public function getCreatedon(): ?\DateTimeInterface
    {
        return $this->createdon;
    }

    public function setCreatedon(\DateTimeInterface $createdon): static
    {
        $this->createdon = $createdon;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }


    private function generateCode(): string
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil(self::CODELENGTH / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil(self::CODELENGTH / 2));
        } else {
            return "";
        }
        return substr(bin2hex($bytes), 0, self::CODELENGTH);



    }

    /**
     * @return Collection<int, Rotation>
     */
    public function getRotations(): Collection
    {
        return $this->rotations;
    }

    public function addRotation(Rotation $rotation): static
    {
        if (!$this->rotations->contains($rotation)) {
            $this->rotations->add($rotation);
            $rotation->setHotbox($this);
        }

        return $this;
    }

    public function removeRotation(Rotation $rotation): static
    {
        if ($this->rotations->removeElement($rotation)) {
            // set the owning side to null (unless already changed)
            if ($rotation->getHotbox() === $this) {
                $rotation->setHotbox(null);
            }
        }

        return $this;
    }

    public function getCurrentRotation(): Rotation|null
    {
        $rotations = $this->rotations->toArray();
        if (empty($rotations)) {
            return null;
        }

        usort($rotations, function(Rotation $a, Rotation $b){
            if ($a->getStart() === $b->getStart()) {
                return 0;
            }
            return $a->getStart() > $b->getStart() ? 1 : -1;
        });

        return $rotations[0];
    }

    public function getRotationFrequency(): ?string
    {
        return $this->rotationFrequency;
    }

    public function setRotationFrequency(string $RotationFrequency): static
    {
        $this->rotationFrequency = $RotationFrequency;
        return $this;
    }

    public function getImageWidth(): ?int
    {
        return $this->imageWidth;
    }

    public function setImageWidth(int $imageWidth): static
    {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    public function getImageHeight(): ?int
    {
        return $this->imageHeight;
    }

    public function setImageHeight(int $imageHeight): static
    {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    public function clearRotations(): self
    {
        $this->rotations = new ArrayCollection();
        return $this;
    }
}
