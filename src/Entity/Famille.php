<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilleRepository")
 */
class Famille
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FamillePersonne", mappedBy="famille")
     */
    private $famillePersonnes;
    
    /**
     * @ORM\Column(type="string", length=10)
     */
    private $clef;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lieu", inversedBy="familles")
     */
    private $lieux;

    public function __construct()
    {
        $this->famillePersonnes = new ArrayCollection();
        $this->lieux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    
    public function getNomComplet(){
        return $this->nom;
    }

    public function getClef(): ?string
    {
        return $this->clef;
    }

    public function setClef(string $clef): self
    {
        $this->clef = $clef;

        return $this;
    }

    /**
     * @return Collection|FamillePersonne[]
     */
    public function getFamillePersonnes(): Collection
    {
        return $this->famillePersonnes;
    }

    public function addFamillePersonne(FamillePersonne $famillePersonne): self
    {
        if (!$this->famillePersonnes->contains($famillePersonne)) {
            $this->famillePersonnes[] = $famillePersonne;
            $famillePersonne->setFamille($this);
        }

        return $this;
    }

    public function removeFamillePersonne(FamillePersonne $famillePersonne): self
    {
        if ($this->famillePersonnes->contains($famillePersonne)) {
            $this->famillePersonnes->removeElement($famillePersonne);
            // set the owning side to null (unless already changed)
            if ($famillePersonne->getFamille() === $this) {
                $famillePersonne->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
    }

    public function addLieux(Lieu $lieux): self
    {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux[] = $lieux;
        }

        return $this;
    }

    public function removeLieux(Lieu $lieux): self
    {
        if ($this->lieux->contains($lieux)) {
            $this->lieux->removeElement($lieux);
        }

        return $this;
    }
}
