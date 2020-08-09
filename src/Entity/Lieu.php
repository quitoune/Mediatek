<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LieuRepository")
 */
class Lieu
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_voie;
    
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mention;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $voie;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complement;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $code_postal;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personne", mappedBy="lieu")
     */
    private $personnes;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EpisodePersonne", mappedBy="lieu")
     */
    private $episodePersonnes;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LivrePersonne", mappedBy="lieu")
     */
    private $livrePersonnes;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FilmPersonne", mappedBy="lieu")
     */
    private $filmPersonnes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Famille", mappedBy="lieux")
     */
    private $familles;

    public function __construct()
    {
        $this->personnes = new ArrayCollection();
        $this->episodePersonnes = new ArrayCollection();
        $this->livrePersonnes = new ArrayCollection();
        $this->filmPersonnes = new ArrayCollection();
        $this->familles = new ArrayCollection();
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

    public function getNumeroVoie(): ?int
    {
        return $this->numero_voie;
    }

    public function setNumeroVoie(?int $numero_voie): self
    {
        $this->numero_voie = $numero_voie;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(?string $mention): self
    {
        $this->mention = $mention;

        return $this;
    }

    public function getVoie(): ?string
    {
        return $this->voie;
    }

    public function setVoie(?string $voie): self
    {
        $this->voie = $voie;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->code_postal;
    }

    public function setCodePostal(?int $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
    
    public function getAdresse(){
        $adresse = "";
        
        $adresse .= ($this->numero_voie ? $this->numero_voie : "");
        $adresse .= ($this->mention ? " " .$this->mention : "");
        $adresse .= ($this->numero_voie || $this->mention ? ", " : "");
        $adresse .= $this->voie . "<br>";
        $adresse .= $this->code_postal . " " . $this->ville;
        
        return $adresse;
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
            $personne->setLieu($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->contains($personne)) {
            $this->personnes->removeElement($personne);
            // set the owning side to null (unless already changed)
            if ($personne->getLieu() === $this) {
                $personne->setLieu(null);
            }
        }

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
            $episodePersonne->setLieu($this);
        }

        return $this;
    }

    public function removeEpisodePersonne(EpisodePersonne $episodePersonne): self
    {
        if ($this->episodePersonnes->contains($episodePersonne)) {
            $this->episodePersonnes->removeElement($episodePersonne);
            // set the owning side to null (unless already changed)
            if ($episodePersonne->getLieu() === $this) {
                $episodePersonne->setLieu(null);
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
            $livrePersonne->setLieu($this);
        }

        return $this;
    }

    public function removeLivrePersonne(LivrePersonne $livrePersonne): self
    {
        if ($this->livrePersonnes->contains($livrePersonne)) {
            $this->livrePersonnes->removeElement($livrePersonne);
            // set the owning side to null (unless already changed)
            if ($livrePersonne->getLieu() === $this) {
                $livrePersonne->setLieu(null);
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
            $filmPersonne->setLieu($this);
        }

        return $this;
    }

    public function removeFilmPersonne(FilmPersonne $filmPersonne): self
    {
        if ($this->filmPersonnes->contains($filmPersonne)) {
            $this->filmPersonnes->removeElement($filmPersonne);
            // set the owning side to null (unless already changed)
            if ($filmPersonne->getLieu() === $this) {
                $filmPersonne->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Famille[]
     */
    public function getFamilles(): Collection
    {
        return $this->familles;
    }

    public function addFamille(Famille $famille): self
    {
        if (!$this->familles->contains($famille)) {
            $this->familles[] = $famille;
            $famille->addLieux($this);
        }

        return $this;
    }

    public function removeFamille(Famille $famille): self
    {
        if ($this->familles->contains($famille)) {
            $this->familles->removeElement($famille);
            $famille->removeLieux($this);
        }

        return $this;
    }
}
