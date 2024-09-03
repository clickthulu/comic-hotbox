<?php

namespace App\Entity;

use App\Enumerations\CarouselDisplayTypeEnumeration;
use App\Repository\CarouselRepository;
use App\Traits\CodeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarouselRepository::class)]
class Carousel
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

    #[ORM\OneToMany(mappedBy: 'carousel', targetEntity: CarouselImage::class, orphanRemoval: true)]
    #[ORM\OrderBy(['active' => 'desc', 'ordinal' => 'asc'])]
    private Collection $carouselImages;

    #[ORM\Column]
    private ?int $width = 120;

    #[ORM\Column]
    private ?int $height = 300;

    #[ORM\Column(length: 255)]
    private ?string $displayType = CarouselDisplayTypeEnumeration::TYPE_FADE;

    #[ORM\Column]
    private ?int $delay = null;

    public function __construct()
    {
        $this->carouselImages = new ArrayCollection();
        $this->createdon = new \DateTime();
        $this->code = $this->generateCode();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, CarouselImage>
     */
    public function getCarouselImages(): Collection
    {
        return $this->carouselImages;
    }

    public function getLastCarouselImage(): ?CarouselImage
    {
        /**
         * @var ?CarouselImage $cimage;
         */
        $cimage = null;
        /**
         * @var CarouselImage $carouselImage
         */
        foreach ($this->carouselImages as $carouselImage) {
            if (empty($cimage) || $cimage->getOrdinal() <= $carouselImage->getOrdinal()) {
                $cimage = $carouselImage;
            }
        }
        return $cimage;
    }

    public function addCarouselImage(CarouselImage $carouselImage): static
    {
        if (!$this->carouselImages->contains($carouselImage)) {
            $this->carouselImages->add($carouselImage);
            $carouselImage->setCarousel($this);
        }

        return $this;
    }

    public function removeCarouselImage(CarouselImage $carouselImage): static
    {
        if ($this->carouselImages->removeElement($carouselImage)) {
            // set the owning side to null (unless already changed)
            if ($carouselImage->getCarousel() === $this) {
                $carouselImage->setCarousel(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;

    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     */
    public function setWidth(?int $width): self
    {
        $this->width = $width;
        return $this;

    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     */
    public function setHeight(?int $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function findCarouselImage(int $id): ?CarouselImage
    {
        /**
         * @var CarouselImage $image
         */
        foreach ($this->carouselImages as $image) {
            if ($image->getComic()->getId() === $id) {
                return $image;
            }
        }
        return null;
    }

    public function getDisplayType(): ?string
    {
        return $this->displayType;
    }

    public function setDisplayType(string $displayType): static
    {
        $this->displayType = $displayType;

        return $this;
    }

    public function getDelay(): ?int
    {
        return $this->delay;
    }

    public function setDelay(int $delay): static
    {
        $this->delay = $delay;

        return $this;
    }


}
