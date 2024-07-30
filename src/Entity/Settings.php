<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    const TYPE_BOOL = 'bool';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_INTEGER = 'integer';
    const TYPE_ARRAY = 'array';
    const TYPE_FILESELECT = 'fileselect';
    const TYPE_CSSSELECT = 'cssselect';
    const TYPE_ENUMERATION = 'enumeration';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $setting = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $modifiedon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $defaultvalue = null;

    #[ORM\Column(length: 255)]
    private ?string $type = 'string';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $help = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $displayName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sourceoptions = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSetting(): ?string
    {
        return $this->setting;
    }

    public function setSetting(string $setting): static
    {
        $this->setting = $setting;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->convertValue($this->value);
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getModifiedon(): ?\DateTimeInterface
    {
        return $this->modifiedon;
    }

    public function setModifiedon(\DateTimeInterface $modifiedon): static
    {
        $this->modifiedon = $modifiedon;

        return $this;
    }

    public function getDefaultvalue(): ?string
    {
        return $this->convertValue($this->defaultvalue);
    }

    public function setDefaultvalue(?string $defaultvalue): static
    {
        $this->defaultvalue = $defaultvalue;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }


    protected function convertValue($value): mixed
    {
        switch(strtolower($this->type)) {
            case self::TYPE_BOOL:
            case self::TYPE_BOOLEAN:
                return $this->toBoolean($value);
            case self::TYPE_INT:
            case self::TYPE_INTEGER:
                return $this->toInteger($value);
            case self::TYPE_STRING:
            default:
                return $value;
        }
    }

    protected function toBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        } elseif (is_int($value)) {
            return $value > 0;
        } elseif (is_string($value)) {
            if ($value === '1' || strtolower($value) === 'true' || strtolower($value) === 't') {
                return true;
            }
        }
        return false;
    }


    protected function toInteger($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        } elseif (is_int($value)) {
            return $value;
        } elseif (is_string($value)) {
            return (int)$value;
        }
        return 0;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function setHelp(?string $help): static
    {
        $this->help = $help;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getSourceoptions(): ?string
    {
        return $this->sourceoptions;
    }

    public function setSourceoptions(?string $sourceoptions): static
    {
        $this->sourceoptions = $sourceoptions;
        return $this;
    }

}
