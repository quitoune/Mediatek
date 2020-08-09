<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EpisodeRepository")
 */
class Episode
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
    private $numero_episode;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $numero_production;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $premiere_diffusion;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $duree;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saison", inversedBy="episodes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $saison;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EpisodePersonne", mappedBy="episode")
     */
    private $episodePersonnes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personne", mappedBy="episode_favori")
     */
    private $personnes;

    public function __construct()
    {
        $this->episodePersonnes = new ArrayCollection();
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
    
    public function getTitreComplet($vo){
        if(is_null($this->titre)){
            return $this->titre_original;
        } else if($vo){
            return $this->titre_original;
        } else {
            return $this->titre;
        }
    }

    public function getNumeroEpisode(): ?int
    {
        return $this->numero_episode;
    }

    public function setNumeroEpisode(int $numero_episode): self
    {
        $this->numero_episode = $numero_episode;

        return $this;
    }

    public function getNumeroProduction(): ?int
    {
        return $this->numero_production;
    }

    public function setNumeroProduction(int $numero_production): self
    {
        $this->numero_production = $numero_production;

        return $this;
    }

    public function getPremiereDiffusion(): ?\DateTimeInterface
    {
        return $this->premiere_diffusion;
    }

    public function setPremiereDiffusion(?\DateTimeInterface $premiere_diffusion): self
    {
        $this->premiere_diffusion = $premiere_diffusion;

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

    public function getSaison(): ?Saison
    {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self
    {
        $this->saison = $saison;

        return $this;
    }

    /**
     * @return Collection|EpisodePersonne[]
     */
    public function getEpisodePersonnes(): Collection
    {
        return $this->episodePersonnes;
    }

    public function addEpisodePersonne(EpisodePersonne $episodePersonne): self
    {
        if (!$this->episodePersonnes->contains($episodePersonne)) {
            $this->episodePersonnes[] = $episodePersonne;
            $episodePersonne->setEpisode($this);
        }

        return $this;
    }

    public function removeEpisodePersonne(EpisodePersonne $episodePersonne): self
    {
        if ($this->episodePersonnes->contains($episodePersonne)) {
            $this->episodePersonnes->removeElement($episodePersonne);
            // set the owning side to null (unless already changed)
            if ($episodePersonne->getEpisode() === $this) {
                $episodePersonne->setEpisode(null);
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
            $personne->setEpisodeFavori($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->contains($personne)) {
            $this->personnes->removeElement($personne);
            // set the owning side to null (unless already changed)
            if ($personne->getEpisodeFavori() === $this) {
                $personne->setEpisodeFavori(null);
            }
        }

        return $this;
    }
}
