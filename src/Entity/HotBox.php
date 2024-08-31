<?php

namespace App\Entity;

use App\Enumerations\RotationFrequencyEnumeration;
use App\Repository\HotBoxRepository;
use App\Traits\CodeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HotBoxRepository::class)]
class HotBox
{
    use CodeTrait;

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

    private int $availableComics = 0;

    private int $activeComics = 0;

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

    public function getUserRotations(User $user): array
    {
        $rotations = [];
        /**
         * @var Rotation $rotation
         */
        foreach ($this->rotations as $rotation) {
            if ($rotation->getComic()->getUser()->getId() === $user->getId()) {
                $rotations[] = $rotation;
            }
        }
        return $rotations;
    }

    public function getUserAllowed(User $user): bool
    {
        $rot = $this->getUserRotations($user);
        return !empty($rot);
    }

    /**
     * @return int
     */
    public function getAvailableComics(): int
    {
        return $this->availableComics;
    }

    /**
     * @param int $availableComics
     */
    public function setAvailableComics(int $availableComics): void
    {
        $this->availableComics = $availableComics;
    }

    /**
     * @return int
     */
    public function getActiveComics(): int
    {
        return $this->activeComics;
    }

    /**
     * @param int $activeComics
     */
    public function setActiveComics(int $activeComics): void
    {
        $this->activeComics = $activeComics;
    }

    public function incAvailable():self
    {
        $this->availableComics++;
        return $this;
    }

    public function incActive(): self
    {
        $this->activeComics++;
        return $this;
    }

    public function needsAttention(): bool
    {
        return $this->availableComics !== $this->activeComics;
    }
}
