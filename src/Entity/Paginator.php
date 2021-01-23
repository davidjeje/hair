<?php

namespace App\Entity;

use App\Repository\PaginatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaginatorRepository::class)
 */
class Paginator
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
    private $page;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbPage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameRoute;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $paramsRoute = [];

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="paginator", orphanRemoval=true)
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getNbPage(): ?int
    {
        return $this->nbPage;
    }

    public function setNbPage(int $nbPage): self
    {
        $this->nbPage = $nbPage;

        return $this;
    }

    public function getNameRoute(): ?string
    {
        return $this->nameRoute;
    }

    public function setNameRoute(string $nameRoute): self
    {
        $this->nameRoute = $nameRoute;

        return $this;
    }

    public function getParamsRoute(): ?array
    {
        return $this->paramsRoute;
    }

    public function setParamsRoute(?array $paramsRoute): self
    {
        $this->paramsRoute = $paramsRoute;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setPaginator($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getPaginator() === $this) {
                $service->setPaginator(null);
            }
        }

        return $this;
    }
}
