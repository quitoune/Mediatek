<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormatRepository")
 */
class Format
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
     * @ORM\Column(type="smallint")
     */
    private $objet;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EpisodePersonne", mappedBy="format")
     */
    private $episodePersonnes;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FilmPersonne", mappedBy="format")
     */
    private $filmPersonnes;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LivrePersonne", mappedBy="format")
     */
    private $livrePersonnes;

    public function __construct()
    {
        $this->episodePersonnes = new ArrayCollection();
        $this->filmPersonnes = new ArrayCollection();
        $this->livrePersonnes = new ArrayCollection();
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

    public function getObjet(): ?int
    {
        return $this->objet;
    }

    public function setObjet(int $objet): self
    {
        $this->objet = $objet;

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
            $episodePersonne->setFormat($this);
        }

        return $this;
    }

    public function removeEpisodePersonne(EpisodePersonne $episodePersonne): self
    {
        if ($this->episodePersonnes->contains($episodePersonne)) {
            $this->episodePersonnes->removeElement($episodePersonne);
            // set the owning side to null (unless already changed)
            if ($episodePersonne->getFormat() === $this) {
                $episodePersonne->setFormat(null);
            }
        }

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
            $filmPersonne->setFormat($this);
        }

        return $this;
    }

    public function removeFilmPersonne(FilmPersonne $filmPersonne): self
    {
        if ($this->filmPersonnes->contains($filmPersonne)) {
            $this->filmPersonnes->removeElement($filmPersonne);
            // set the owning side to null (unless already changed)
            if ($filmPersonne->getFormat() === $this) {
                $filmPersonne->setFormat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LivrePersonne[]
     */
    public function getLivrePersonnes(): Collection
    {
        return $this->livrePersonnes;
    }

    public function addLivrePersonne(LivrePersonne $livrePersonne): self
    {
        if (!$this->livrePersonnes->contains($livrePersonne)) {
            $this->livrePersonnes[] = $livrePersonne;
            $livrePersonne->setFormat($this);
        }

        return $this;
    }

    public function removeLivrePersonne(LivrePersonne $livrePersonne): self
    {
        if ($this->livrePersonnes->contains($livrePersonne)) {
            $this->livrePersonnes->removeElement($livrePersonne);
            // set the owning side to null (unless already changed)
            if ($livrePersonne->getFormat() === $this) {
                $livrePersonne->setFormat(null);
            }
        }

        return $this;
    }
}
