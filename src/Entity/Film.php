<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FilmRepository")
 */
class Film
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titre;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre_original;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $realisateur;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $duree;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $annee;
    
    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $volet;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Categorie", inversedBy="films")
     */
    private $categories;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saga", inversedBy="films")
     */
    private $saga;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="films")
     */
    private $photo;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FilmPersonne", mappedBy="film", cascade={"persist"})
     */
    private $filmPersonnes;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActeurFilm", mappedBy="film")
     * @ORM\OrderBy({"principal" = "DESC"})
     */
    private $acteurFilms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personne", mappedBy="film_favori")
     */
    private $personnes;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->filmPersonnes = new ArrayCollection();
        $this->acteurFilms = new ArrayCollection();
        $this->personnes = new ArrayCollection();
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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTitreOriginal(): ?string
    {
        return $this->titre_original;
    }

    public function setTitreOriginal(?string $titre_original): self
    {
        $this->titre_original = $titre_original;

        return $this;
    }
    
    public function getTitreComplet($vo = 1){
        if(is_null($this->titre)){
            return $this->titre_original;
        } else if($vo){
            return $this->titre_original;
        } else {
            return $this->titre;
        }
    }

    public function getRealisateur(): ?string
    {
        return $this->realisateur;
    }

    public function setRealisateur(string $realisateur): self
    {
        $this->realisateur = $realisateur;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getVolet(): ?int
    {
        return $this->volet;
    }

    public function setVolet(?int $volet): self
    {
        $this->volet = $volet;

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function getSaga(): ?Saga
    {
        return $this->saga;
    }

    public function setSaga(?Saga $saga): self
    {
        $this->saga = $saga;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|FilmPersonne[]
     */
    public function getFilmPersonnes(): Collection
    {
        return $this->filmPersonnes;
    }

    public function addFilmPersonne(FilmPersonne $filmPersonne): self
    {
        if (!$this->filmPersonnes->contains($filmPersonne)) {
            $this->filmPersonnes[] = $filmPersonne;
            $filmPersonne->setFilm($this);
        }

        return $this;
    }

    public function removeFilmPersonne(FilmPersonne $filmPersonne): self
    {
        if ($this->filmPersonnes->contains($filmPersonne)) {
            $this->filmPersonnes->removeElement($filmPersonne);
            // set the owning side to null (unless already changed)
            if ($filmPersonne->getFilm() === $this) {
                $filmPersonne->setFilm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ActeurFilm[]
     */
    public function getActeurFilms(): Collection
    {
        return $this->acteurFilms;
    }

    public function addActeurFilm(ActeurFilm $acteurFilm): self
    {
        if (!$this->acteurFilms->contains($acteurFilm)) {
            $this->acteurFilms[] = $acteurFilm;
            $acteurFilm->setFilm($this);
        }

        return $this;
    }

    public function removeActeurFilm(ActeurFilm $acteurFilm): self
    {
        if ($this->acteurFilms->contains($acteurFilm)) {
            $this->acteurFilms->removeElement($acteurFilm);
            // set the owning side to null (unless already changed)
            if ($acteurFilm->getFilm() === $this) {
                $acteurFilm->setFilm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Personne[]
     */
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes[] = $personne;
            $personne->setFilmFavori($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->contains($personne)) {
            $this->personnes->removeElement($personne);
            // set the owning side to null (unless already changed)
            if ($personne->getFilmFavori() === $this) {
                $personne->setFilmFavori(null);
            }
        }

        return $this;
    }
}
