<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PageRepository::class)
 * @ORM\Table(name="page")
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * 
     * @Assert\NotBlank
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * 
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text", nullable=false)
     * 
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * 
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $private;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(int $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->description;
    }

    public function setType(string $type): self
    {
        $this->description = $type;

        return $this;
    }

    public function getSubModel(): ?int
    {
        return $this->sub_model;
    }

    public function setSubModel(int $sub_model): self
    {
        $this->sub_model = $sub_model;

        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }
}
