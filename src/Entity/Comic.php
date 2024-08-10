<?php

namespace App\Entity;

use App\Repository\ComicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComicRepository::class)]
class Comic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'comic', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $createdon = null;

    #[ORM\Column]
    private ?bool $approved = false;

    #[ORM\Column]
    private ?bool $active = true;

    #[ORM\ManyToOne(inversedBy: 'comics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'comic', targetEntity: Rotation::class, orphanRemoval: true)]
    private Collection $rotations;

    private bool $hotboxMatch = false;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->rotations = new ArrayCollection();
        $this->createdon = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setComic($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getComic() === $this) {
                $image->setComic(null);
            }
        }

        return $this;
    }

    public function getRandomImage(HotBox $hotBox): Image
    {
        $allowed = [];
        /**
         * @var Image $image
         */
        foreach ($this->images as $image) {
            if ($image->isApproved() && $image->isActive() && ($image->getWidth() === $hotBox->getImageWidth()) && ($image->getHeight() === $hotBox->getImageHeight())) {
                $allowed[] = $image;
            }
        }
        shuffle($allowed);
        return array_pop($allowed);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

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

    public function isApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): static
    {
        $this->approved = $approved;

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
            $rotation->setComic($this);
        }

        return $this;
    }

    public function removeRotation(Rotation $rotation): static
    {
        if ($this->rotations->removeElement($rotation)) {
            // set the owning side to null (unless already changed)
            if ($rotation->getComic() === $this) {
                $rotation->setComic(null);
            }
        }

        return $this;
    }

    public function clearRotations(): static
    {
        $this->rotations->clear();
        return $this;
    }

    public function clearRotationsFromHotBox(HotBox $hotBox): static
    {

        foreach ($this->getRotations() as $rotation) {
            if ($rotation->getHotbox() === $hotBox) {
                $hotBox->removeRotation($rotation);
                $this->removeRotation($rotation);
            }
        }
        return $this;
    }

    public function imageSizeMatch(Hotbox $hotbox): bool
    {
        /**
         * @var Image $image
         */
        foreach ($this->images as &$image)
        {
            if ($image->isApproved() && $image->isActive() && $image->getWidth() === $hotbox->getImageWidth() && $image->getHeight() === $hotbox->getImageHeight()) {
                $this->hotboxMatch = true;
                return true;
            }

        }
        $this->hotboxMatch = false;
        return false;
    }

    public function setImageHotboxMatch(array $hotboxes): self
    {
        /**
         * @var HotBox $hotbox
         */
        foreach ($hotboxes as $hotbox) {
            foreach ($this->images as &$image) {
                if ($image->getWidth() === $hotbox->getImageWidth() && $image->getHeight() === $hotbox->getImageHeight()) {
                    $image->setMatchesHotboxSize(true);
                }
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isHotboxMatch(): bool
    {
        return $this->hotboxMatch;
    }

    /**
     * @param bool $hotboxMatch
     */
    public function setHotboxMatch(bool $hotboxMatch): void
    {
        $this->hotboxMatch = $hotboxMatch;
    }


}
