<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "URL не может быть пустым")]
    #[Assert\Url(message: "Введён невалидный URL! Проверьте ссылку ещё раз!")]
    private ?string $original_url = null;

    #[ORM\Column(length: 255)]
    private ?string $short_code = null;

    #[ORM\Column]
    private ?\DateTime $creation_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $last_click_time_date = null;

    #[ORM\Column(nullable: true)]
    private ?int $click_count = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_one_time = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan("today", message: "Дата должна быть в будущем")]
    private ?\DateTimeImmutable $expiration_date = null;

    public function getIsOneTime(): ?bool
    {
        return $this->is_one_time;
    }

    public function setIsOneTime(?bool $is_one_time): static
    {
        $this->is_one_time = $is_one_time;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(?\DateTimeImmutable $expiration_date): static
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

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
