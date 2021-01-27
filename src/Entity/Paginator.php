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
<<<<<<< HEAD
    private $nbPage;
=======
    private $nbPages;
>>>>>>> paginator

    /**
     * @ORM\Column(type="string", length=255)
     */
<<<<<<< HEAD
    private $nameRoute;
=======
    private $nomRoute;
>>>>>>> paginator

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $paramsRoute = [];

    /**
<<<<<<< HEAD
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="paginator", orphanRemoval=true)
=======
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="paginator")
>>>>>>> paginator
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

<<<<<<< HEAD
    public function getNbPage(): ?int
    {
        return $this->nbPage;
    }

    public function setNbPage(int $nbPage): self
    {
        $this->nbPage = $nbPage;
=======
    public function getNbPages(): ?int
    {
        return $this->nbPages;
    }

    public function setNbPages(int $nbPages): self
    {
        $this->nbPages = $nbPages;
>>>>>>> paginator

        return $this;
    }

<<<<<<< HEAD
    public function getNameRoute(): ?string
    {
        return $this->nameRoute;
    }

    public function setNameRoute(string $nameRoute): self
    {
        $this->nameRoute = $nameRoute;
=======
    public function getNomRoute(): ?string
    {
        return $this->nomRoute;
    }

    public function setNomRoute(string $nomRoute): self
    {
        $this->nomRoute = $nomRoute;
>>>>>>> paginator

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
