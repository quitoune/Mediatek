<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SerieRepository")
 */
class Serie
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
     * @ORM\Column(type="integer")
     */
    private $annee;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_saison;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_episode;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $terminee;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Categorie", mappedBy="series")
     */
    private $categories;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saga", inversedBy="series")
     */
    private $saga;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="series")
     */
    private $photo;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Saison", mappedBy="serie")
     * @ORM\OrderBy({"numero_saison" = "ASC"})
     */
    private $saisons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personne", mappedBy="serie_favorie")
     */
    private $personnes;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->saisons = new ArrayCollection();
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

    public function setTitreOriginal(string $titre_original): self
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

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getNombreSaison(): ?int
    {
        return $this->nombre_saison;
    }

    public function setNombreSaison(int $nombre_saison): self
    {
        $this->nombre_saison = $nombre_saison;

        return $this;
    }

    public function getNombreEpisode(): ?int
    {
        return $this->nombre_episode;
    }

    public function setNombreEpisode(int $nombre_episode): self
    {
        $this->nombre_episode = $nombre_episode;

        return $this;
    }

    public function getTerminee(): ?bool
    {
        return $this->terminee;
    }

    public function setTerminee(bool $terminee): self
    {
        $this->terminee = $terminee;

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
            $category->addSeries($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeSeries($this);
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
            $saison->setSerie($this);
        }

        return $this;
    }

    public function removeSaison(Saison $saison): self
    {
        if ($this->saisons->contains($saison)) {
            $this->saisons->removeElement($saison);
            // set the owning side to null (unless already changed)
            if ($saison->getSerie() === $this) {
                $saison->setSerie(null);
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
            $personne->setSerieFavorie($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->contains($personne)) {
            $this->personnes->removeElement($personne);
            // set the owning side to null (unless already changed)
            if ($personne->getSerieFavorie() === $this) {
                $personne->setSerieFavorie(null);
            }
        }

        return $this;
    }
}
