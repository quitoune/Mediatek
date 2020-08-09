<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;
    
    /**
     * @ORM\Column(type="text")
     */
    private $chemin;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Serie", mappedBy="photo")
     */
    private $series;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Saison", mappedBy="photo")
     */
    private $saisons;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acteur", mappedBy="photo")
     */
    private $acteurs;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Livre", mappedBy="photo")
     */
    private $livres;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Film", mappedBy="photo")
     */
    private $films;

    public function __construct()
    {
        $this->series = new ArrayCollection();
        $this->saisons = new ArrayCollection();
        $this->acteurs = new ArrayCollection();
        $this->livres = new ArrayCollection();
        $this->films = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getChemin(): ?string
    {
        return $this->chemin;
    }

    public function setChemin(string $chemin): self
    {
        $this->chemin = $chemin;

        return $this;
    }

    /**
     * @return Collection|Serie[]
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Serie $series): self
    {
        if (!$this->series->contains($series)) {
            $this->series[] = $series;
            $series->setPhoto($this);
        }

        return $this;
    }

    public function removeSeries(Serie $series): self
    {
        if ($this->series->contains($series)) {
            $this->series->removeElement($series);
            // set the owning side to null (unless already changed)
            if ($series->getPhoto() === $this) {
                $series->setPhoto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Saison[]
     */
    public function getSaisons(): Collection
    {
        return $this->saisons;
    }

    public function addSaison(Saison $saison): self
    {
        if (!$this->saisons->contains($saison)) {
            $this->saisons[] = $saison;
            $saison->setPhoto($this);
        }

        return $this;
    }

    public function removeSaison(Saison $saison): self
    {
        if ($this->saisons->contains($saison)) {
            $this->saisons->removeElement($saison);
            // set the owning side to null (unless already changed)
            if ($saison->getPhoto() === $this) {
                $saison->setPhoto(null);
            }
        }

        return $this;
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
            $acteur->setPhoto($this);
        }

        return $this;
    }

    public function removeActeur(Acteur $acteur): self
    {
        if ($this->acteurs->contains($acteur)) {
            $this->acteurs->removeElement($acteur);
            // set the owning side to null (unless already changed)
            if ($acteur->getPhoto() === $this) {
                $acteur->setPhoto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): self
    {
        if (!$this->livres->contains($livre)) {
            $this->livres[] = $livre;
            $livre->setPhoto($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): self
    {
        if ($this->livres->contains($livre)) {
            $this->livres->removeElement($livre);
            // set the owning side to null (unless already changed)
            if ($livre->getPhoto() === $this) {
                $livre->setPhoto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Film[]
     */
    public function getFilms(): Collection
    {
        return $this->films;
    }

    public function addFilm(Film $film): self
    {
        if (!$this->films->contains($film)) {
            $this->films[] = $film;
            $film->setPhoto($this);
        }

        return $this;
    }

    public function removeFilm(Film $film): self
    {
        if ($this->films->contains($film)) {
            $this->films->removeElement($film);
            // set the owning side to null (unless already changed)
            if ($film->getPhoto() === $this) {
                $film->setPhoto(null);
            }
        }

        return $this;
    }
}
