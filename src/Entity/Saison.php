<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SaisonRepository")
 */
class Saison
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
    private $nom;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $numero_saison;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_episode;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Serie", inversedBy="saisons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $serie;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="saisons")
     */
    private $photo;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActeurSaison", mappedBy="saison")
     */
    private $acteurSaisons;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Episode", mappedBy="saison")
     * @ORM\OrderBy({"numero_episode" = "ASC"})
     */
    private $episodes;

    public function __construct()
    {
        $this->acteurSaisons = new ArrayCollection();
        $this->episodes = new ArrayCollection();
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

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    
    public function getNomComplet($vo){
        if(is_null($this->nom)){
            return $this->getSerie()->getTitreComplet($vo) . ' - Saison ' . $this->numero_saison;
        } else {
            return $this->getSerie()->getTitreComplet($vo) . ' - ' . $this->nom;
        }
    }

    public function getNumeroSaison(): ?int
    {
        return $this->numero_saison;
    }

    public function setNumeroSaison(int $numero_saison): self
    {
        $this->numero_saison = $numero_saison;

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

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): self
    {
        $this->serie = $serie;

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
            $acteurSaison->setSaison($this);
        }

        return $this;
    }

    public function removeActeurSaison(ActeurSaison $acteurSaison): self
    {
        if ($this->acteurSaisons->contains($acteurSaison)) {
            $this->acteurSaisons->removeElement($acteurSaison);
            // set the owning side to null (unless already changed)
            if ($acteurSaison->getSaison() === $this) {
                $acteurSaison->setSaison(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Episode[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setSaison($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->contains($episode)) {
            $this->episodes->removeElement($episode);
            // set the owning side to null (unless already changed)
            if ($episode->getSaison() === $this) {
                $episode->setSaison(null);
            }
        }

        return $this;
    }
}
