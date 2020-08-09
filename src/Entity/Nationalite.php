<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NationaliteRepository")
 */
class Nationalite
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
    private $pays;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $feminin;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $masculin;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Acteur", mappedBy="nationalites")
     */
    private $acteurs;

    public function __construct()
    {
        $this->acteurs = new ArrayCollection();
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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getFeminin(): ?string
    {
        return $this->feminin;
    }

    public function setFeminin(string $feminin): self
    {
        $this->feminin = $feminin;

        return $this;
    }

    public function getMasculin(): ?string
    {
        return $this->masculin;
    }

    public function setMasculin(?string $masculin): self
    {
        $this->masculin = $masculin;

        return $this;
    }
    
    public function getNomComplet(){
        if(is_null($this->etat)){
            return $this->pays;
        } else {
            return $this->pays . " (" . $this->etat . ")";
        }
    }

    /**
     * @return Collection|Acteur[]
     */
    public function getActeurs(): Collection
    {
        return $this->acteurs;
    }

    public function addActeur(Acteur $acteur): self
    {
        if (!$this->acteurs->contains($acteur)) {
            $this->acteurs[] = $acteur;
            $acteur->addNationalite($this);
        }

        return $this;
    }

    public function removeActeur(Acteur $acteur): self
    {
        if ($this->acteurs->contains($acteur)) {
            $this->acteurs->removeElement($acteur);
            $acteur->removeNationalite($this);
        }

        return $this;
    }
}
