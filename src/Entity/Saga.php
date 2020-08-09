<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SagaRepository")
 */
class Saga
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom_original;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saga", inversedBy="sous_sagas")
     */
    private $saga;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Serie", mappedBy="saga")
     * @ORM\OrderBy({"titre_original" = "ASC"})
     */
    private $series;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Livre", mappedBy="saga")
     * @ORM\OrderBy({"tome" = "ASC"})
     */
    private $livres;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Film", mappedBy="saga")
     * @ORM\OrderBy({"volet" = "ASC"})
     */
    private $films;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Saga", mappedBy="saga")
     */
    private $sous_sagas;

    public function __construct()
    {
        $this->series = new ArrayCollection();
        $this->livres = new ArrayCollection();
        $this->films = new ArrayCollection();
        $this->sous_sagas = new ArrayCollection();
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
    
    public function getNomComplet()
    {
        if(is_null($this->getSaga())){
            return $this->nom;
        } else {
            return $this->getSaga()->getNomComplet() . " - " . $this->nom;
        }
    }

    public function getNomOriginal(): ?string
    {
        return $this->nom_original;
    }

    public function setNomOriginal(?string $nom_original): self
    {
        $this->nom_original = $nom_original;

        return $this;
    }

    public function getSaga(): ?self
    {
        return $this->saga;
    }

    public function setSaga(?self $saga): self
    {
        $this->saga = $saga;

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
            $series->setSaga($this);
        }

        return $this;
    }

    public function removeSeries(Serie $series): self
    {
        if ($this->series->contains($series)) {
            $this->series->removeElement($series);
            // set the owning side to null (unless already changed)
            if ($series->getSaga() === $this) {
                $series->setSaga(null);
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
            $livre->setSaga($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): self
    {
        if ($this->livres->contains($livre)) {
            $this->livres->removeElement($livre);
            // set the owning side to null (unless already changed)
            if ($livre->getSaga() === $this) {
                $livre->setSaga(null);
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
            $film->setSaga($this);
        }

        return $this;
    }

    public function removeFilm(Film $film): self
    {
        if ($this->films->contains($film)) {
            $this->films->removeElement($film);
            // set the owning side to null (unless already changed)
            if ($film->getSaga() === $this) {
                $film->setSaga(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Saga[]
     */
    public function getSousSagas(): Collection
    {
        return $this->sous_sagas;
    }

    public function addSousSaga(Saga $sousSaga): self
    {
        if (!$this->sous_sagas->contains($sousSaga)) {
            $this->sous_sagas[] = $sousSaga;
            $sousSaga->setSaga($this);
        }

        return $this;
    }

    public function removeSousSaga(Saga $sousSaga): self
    {
        if ($this->sous_sagas->contains($sousSaga)) {
            $this->sous_sagas->removeElement($sousSaga);
            // set the owning side to null (unless already changed)
            if ($sousSaga->getSaga() === $this) {
                $sousSaga->setSaga(null);
            }
        }

        return $this;
    }
}
