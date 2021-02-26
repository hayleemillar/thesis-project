<?php

namespace App\Entity;

use App\Repository\UserSubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserSubscriptionRepository::class)
 * @ORM\Table(name="user_sub")
 */
class UserSubscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $sub_tier;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubTier(): ?int
    {
        return $this->sub_tier;
    }

    public function setSubTier(int $sub_tier): self
    {
        $this->sub_tier = $sub_tier;

        return $this;
    }
}
