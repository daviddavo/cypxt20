<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LineRepository")
 */
class Line
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=31)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_open;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_close;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLastOpen(): ?\DateTimeInterface
    {
        return $this->last_open;
    }

    public function setLastOpen(\DateTimeInterface $last_open): self
    {
        $this->last_open = $last_open;

        return $this;
    }

    public function getLastClose(): ?\DateTimeInterface
    {
        return $this->last_close;
    }

    public function setLastClose(\DateTimeInterface $last_close): self
    {
        $this->last_close = $last_close;

        return $this;
    }
}
