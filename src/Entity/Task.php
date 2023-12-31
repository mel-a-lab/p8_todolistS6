<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "task")]
class Task
{
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "Vous devez saisir un titre.")]
    private string $title;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Vous devez saisir du contenu.")]
    private string $content;

    #[ORM\Column(type: "boolean")]
    private bool $isDone;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isDone = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function toggle(bool $flag): void
    {
        $this->isDone = $flag;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}