<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $original_url = null;

    #[ORM\Column(length: 255)]
    private ?string $short_code = null;

    #[ORM\Column]
    private ?\DateTime $creation_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $last_click_time_date = null;

    #[ORM\Column(nullable: true)]
    private ?int $click_count = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalUrl(): ?string
    {
        return $this->original_url;
    }

    public function setOriginalUrl(string $original_url): static
    {
        $this->original_url = $original_url;

        return $this;
    }

    public function getShortCode(): ?string
    {
        return $this->short_code;
    }

    public function setShortCode(string $short_code): static
    {
        $this->short_code = $short_code;

        return $this;
    }

    public function getCreationDate(): ?\DateTime
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTime $creation_date): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getLastClickTimeDate(): ?\DateTime
    {
        return $this->last_click_time_date;
    }

    public function setLastClickTimeDate(?\DateTime $last_click_time_date): static
    {
        $this->last_click_time_date = $last_click_time_date;

        return $this;
    }

    public function getClickCount(): ?int
    {
        return $this->click_count;
    }

    public function setClickCount(?int $click_count): static
    {
        $this->click_count = $click_count;

        return $this;
    }
}
