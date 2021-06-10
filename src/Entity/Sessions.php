<?php

namespace App\Entity;

use App\Repository\SessionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SessionsRepository::class)
 */
class Sessions
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $sess_id;

    /**
     * @ORM\Column(type="binary")
     */
    private $sess_data;

    /**
     * @ORM\Column(type="integer")
     */
    private $sess_lifetime;

    /**
     * @ORM\Column(type="integer")
     */
    private $sess_time;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Sessions")
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessId(): ?string
    {
        return $this->sess_id;
    }

    public function setSessId(string $sess_id): self
    {
        $this->sess_id = $sess_id;

        return $this;
    }

    public function getSessData()
    {
        return $this->sess_data;
    }

    public function setSessData($sess_data): self
    {
        $this->sess_data = $sess_data;

        return $this;
    }

    public function getSessLifetime(): ?int
    {
        return $this->sess_lifetime;
    }

    public function setSessLifetime(int $sess_lifetime): self
    {
        $this->sess_lifetime = $sess_lifetime;

        return $this;
    }

    public function getSessTime(): ?int
    {
        return $this->sess_time;
    }

    public function setSessTime(int $sess_time): self
    {
        $this->sess_time = $sess_time;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }
}
