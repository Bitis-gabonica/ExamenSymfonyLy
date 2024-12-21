<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column]
    private ?float $total = null;

    /**
     * @var Collection<int, Aprovisionnement>
     */
    #[ORM\OneToMany(targetEntity: Aprovisionnement::class, mappedBy: 'commande',cascade: ['persist', 'remove'])]
    private Collection $aprovisionnements;

    public function __construct()
    {
        $this->aprovisionnements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, Aprovisionnement>
     */
    public function getAprovisionnements(): Collection
    {
        return $this->aprovisionnements;
    }

    public function addAprovisionnement(Aprovisionnement $aprovisionnement): static
    {
        if (!$this->aprovisionnements->contains($aprovisionnement)) {
            $this->aprovisionnements->add($aprovisionnement);
            $aprovisionnement->setCommande($this);
        }

        return $this;
    }

    public function removeAprovisionnement(Aprovisionnement $aprovisionnement): static
    {
        if ($this->aprovisionnements->removeElement($aprovisionnement)) {
            // set the owning side to null (unless already changed)
            if ($aprovisionnement->getCommande() === $this) {
                $aprovisionnement->setCommande(null);
            }
        }

        return $this;
    }
}
