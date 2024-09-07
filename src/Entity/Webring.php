<?php

namespace App\Entity;

use App\Repository\WebringRepository;
use App\Traits\CodeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebringRepository::class)]
class Webring
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

    #[ORM\Column]
    private ?int $numberImages = 5;

    #[ORM\Column]
    private ?int $navigationWidth = 20;

    #[ORM\Column]
    private ?int $ringWidth = 720;

    #[ORM\Column]
    private ?int $ringHeight = 90;

    #[ORM\OneToMany(mappedBy: 'webring', targetEntity: WebringImage::class, orphanRemoval: true)]
    #[ORM\OrderBy(['active' => 'desc', 'ordinal' => 'asc'])]
    private Collection $webringImages;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $navigationLeft = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $navigationRight = null;

    public function __construct()
    {
        $this->createdon = new \DateTime();
        $this->code = $this->generateCode();
        $this->webringImages = new ArrayCollection();
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


    public function getNumberImages(): ?int
    {
        return $this->numberImages;
    }

    public function setNumberImages(int $numberImages): static
    {
        $this->numberImages = $numberImages;

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
    public function getNavigationWidth(): ?int
    {
        return $this->navigationWidth;
    }

    /**
     * @param int|null $navigationWidth
     */
    public function setNavigationWidth(?int $navigationWidth): self
    {
        $this->navigationWidth = $navigationWidth;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRingWidth(): ?int
    {
        return $this->ringWidth;
    }

    /**
     * @param int|null $ringWidth
     */
    public function setRingWidth(?int $ringWidth): self
    {
        $this->ringWidth = $ringWidth;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRingHeight(): ?int
    {
        return $this->ringHeight;
    }

    /**
     * @param int|null $ringHeight
     */
    public function setRingHeight(?int $ringHeight): self
    {
        $this->ringHeight = $ringHeight;
        return $this;
    }

    /**
     * @return Collection<int, WebringImage>
     */
    public function getWebringImages(): Collection
    {
        return $this->webringImages;
    }

    public function addWebringImage(WebringImage $webringImage): static
    {
        if (!$this->webringImages->contains($webringImage)) {
            $this->webringImages->add($webringImage);
            $webringImage->setWebring($this);
        }

        return $this;
    }

    public function removeWebringImage(WebringImage $webringImage): static
    {
        if ($this->webringImages->removeElement($webringImage)) {
            // set the owning side to null (unless already changed)
            if ($webringImage->getWebring() === $this) {
                $webringImage->setWebring(null);
            }
        }

        return $this;
    }


    public function findWebringImage(int $id): ?WebringImage
    {
        /**
         * @var WebringImage $image
         */
        foreach ($this->webringImages as $image) {
            if ($image->getComic()->getId() === $id) {
                return $image;
            }
        }
        return null;
    }

    public function calculateImageWidth(): int
    {
        return floor($this->ringWidth - 2* $this->navigationWidth)/$this->numberImages;
    }

    public function calculateImageHeight(): int
    {
        return floor($this->ringHeight);
    }


    public function getLastWebringImage(): ?WebringImage
    {
        /**
         * @var ?WebringImage $wimage;
         */
        $wimage = null;
        /**
         * @var CarouselImage $carouselImage
         */
        foreach ($this->webringImages as $webringImage) {
            if (empty($wimage) || $wimage->getOrdinal() <= $webringImage->getOrdinal()) {
                $wimage = $webringImage;
            }
        }
        return $wimage;
    }

    public function comicMatch(Comic $comic): bool
    {
        foreach ($this->getWebringImages() as $webringImage) {
            if ($webringImage->getComic() === $comic) {
                return true;
            }
        }
        return false;
    }

    public function getNavigationLeft(): ?string
    {
        return $this->navigationLeft;
    }

    public function setNavigationLeft(?string $navigationLeft): static
    {
        $this->navigationLeft = $navigationLeft;

        return $this;
    }

    public function getNavigationRight(): ?string
    {
        return $this->navigationRight;
    }

    public function setNavigationRight(?string $navigationRight): static
    {
        $this->navigationRight = $navigationRight;

        return $this;
    }

}
