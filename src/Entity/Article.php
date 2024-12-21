<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 55)]
    private ?string $nom = null;

    #[Assert\Positive]
    #[ORM\Column]
    private ?float $prix = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column]
    private ?int $qteStock = null;

    /**
     * @var Collection<int, Aprovisionnement>
     */
    #[ORM\OneToMany(targetEntity: Aprovisionnement::class, mappedBy: 'article', cascade: ['persist', 'remove'])]
    private Collection $aprovisionnements;

    public function __construct()
    {
        $this->aprovisionnements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQteStock(): ?int
    {
        return $this->qteStock;
    }

    public function setQteStock(int $qteStock): static
    {
        $this->qteStock = $qteStock;

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
            $aprovisionnement->setArticle($this);
        }

        return $this;
    }

    public function removeAprovisionnement(Aprovisionnement $aprovisionnement): static
    {
        if ($this->aprovisionnements->removeElement($aprovisionnement)) {
            // set the owning side to null (unless already changed)
            if ($aprovisionnement->getArticle() === $this) {
                $aprovisionnement->setArticle(null);
            }
        }

        return $this;
    }
}
