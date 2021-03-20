<?php

namespace App\Entity;

use DateTime;
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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone_number;

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

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function toggleStatus(): self
    {
        if ($this->status == 'idle') {
            $this->status = 'busy';
            $this->last_open = new DateTime();
        } else {
            $this->status = 'idle';
            $this->last_close = new DateTime();
        }

        return $this;
    }
}
