<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActeurRepository")
 */
class Acteur
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
    private $prenom;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom_usage;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom_naissance;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_naissance;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_deces;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $sexe;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Nationalite", inversedBy="acteurs")
     */
    private $nationalites;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="acteurs")
     */
    private $photo;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActeurSaison", mappedBy="acteur")
     */
    private $acteurSaisons;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActeurFilm", mappedBy="acteur")
     */
    private $acteurFilms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personne", mappedBy="acteur_favori")
     */
    private $personnes;

    public function __construct()
    {
        $this->nationalites = new ArrayCollection();
        $this->acteurSaisons = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNomUsage(): ?string
    {
        return $this->nom_usage;
    }

    public function setNomUsage(?string $nom_usage): self
    {
        $this->nom_usage = $nom_usage;

        return $this;
    }
    
    public function getNomComplet(){
        if(is_null($this->nom_usage)){
            if(!is_null($this->nom) && !is_null($this->prenom)){
                return $this->prenom . " " . $this->nom;
            } else if(!is_null($this->nom)){
                return $this->nom;
            } else if(!is_null($this->prenom)){
                return $this->prenom;
            }
        }
        return $this->nom_usage;
    }

    public function getNomNaissance(): ?string
    {
        return $this->nom_naissance;
    }

    public function setNomNaissance(?string $nom_naissance): self
    {
        $this->nom_naissance = $nom_naissance;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }
    
    public function getAnneeNaissance(): ?int
    {
        if(is_null($this->date_naissance)){
            return null;
        } else {
            return $this->date_naissance->format('Y');
        }
    }

    public function getDateDeces(): ?\DateTimeInterface
    {
        return $this->date_deces;
    }

    public function setDateDeces(?\DateTimeInterface $date_deces): self
    {
        $this->date_deces = $date_deces;

        return $this;
    }

    public function getSexe(): ?bool
    {
        return $this->sexe;
    }

    public function setSexe(bool $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * @return Collection|Nationalite[]
     */
    public function getNationalites(): Collection
    {
        return $this->nationalites;
    }

    public function addNationalite(Nationalite $nationalite): self
    {
        if (!$this->nationalites->contains($nationalite)) {
            $this->nationalites[] = $nationalite;
        }

        return $this;
    }

    public function removeNationalite(Nationalite $nationalite): self
    {
        if ($this->nationalites->contains($nationalite)) {
            $this->nationalites->removeElement($nationalite);
        }

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
     * @return Collection|ActeurSaison[]
     */
    public function getActeurSaisons(): Collection
    {
        return $this->acteurSaisons;
    }

    public function addActeurSaison(ActeurSaison $acteurSaison): self
    {
        if (!$this->acteurSaisons->contains($acteurSaison)) {
            $this->acteurSaisons[] = $acteurSaison;
            $acteurSaison->setActeur($this);
        }

        return $this;
    }

    public function removeActeurSaison(ActeurSaison $acteurSaison): self
    {
        if ($this->acteurSaisons->contains($acteurSaison)) {
            $this->acteurSaisons->removeElement($acteurSaison);
            // set the owning side to null (unless already changed)
            if ($acteurSaison->getActeur() === $this) {
                $acteurSaison->setActeur(null);
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
            $acteurFilm->setActeur($this);
        }

        return $this;
    }

    public function removeActeurFilm(ActeurFilm $acteurFilm): self
    {
        if ($this->acteurFilms->contains($acteurFilm)) {
            $this->acteurFilms->removeElement($acteurFilm);
            // set the owning side to null (unless already changed)
            if ($acteurFilm->getActeur() === $this) {
                $acteurFilm->setActeur(null);
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
            $personne->setActeurFavori($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->contains($personne)) {
            $this->personnes->removeElement($personne);
            // set the owning side to null (unless already changed)
            if ($personne->getActeurFavori() === $this) {
                $personne->setActeurFavori(null);
            }
        }

        return $this;
    }
}
