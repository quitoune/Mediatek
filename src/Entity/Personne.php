<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\PersonneRepository")
 */
class Personne implements AdvancedUserInterface
{
    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;
    
    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;
    
    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;
    
    /**
     *
     * @ORM\Column(type="text")
     */
    private $password;
    
    /**
     *
     * @ORM\Column(type="array")
     */
    private $roles = [];
    
    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $salt;
    
    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $email;
    
    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $film_vo;
    
    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $livre_vo;
    
    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $episode_vo;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="personnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Avatar", inversedBy="personnes")
     */
    private $avatar;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\EpisodePersonne", mappedBy="personne")
     */
    private $episodePersonnes;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LivrePersonne", mappedBy="personne")
     */
    private $livrePersonnes;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FilmPersonne", mappedBy="personne")
     */
    private $filmPersonnes;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FamillePersonne", mappedBy="personne")
     */
    private $famillePersonnes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Livre", inversedBy="personnes")
     */
    private $livre_favori;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Film", inversedBy="personnes")
     */
    private $film_favori;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Serie", inversedBy="personnes")
     */
    private $serie_favorie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Episode", inversedBy="personnes")
     */
    private $episode_favori;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Acteur", inversedBy="personnes")
     */
    private $acteur_favori;

    public function __construct()
    {
        $this->episodePersonnes = new ArrayCollection();
        $this->livrePersonnes = new ArrayCollection();
        $this->filmPersonnes = new ArrayCollection();
        $this->famillePersonnes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUsername(): ?string
    {
        return $this->username;
    }
    
    public function setUsername(string $username): self
    {
        $this->username = $username;
        
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
    
    public function getNomComplet(){
        if(!is_null($this->prenom) && !is_null($this->nom)){
            return $this->prenom . ' ' . $this->nom;
        } else if(!is_null($this->prenom)){
            return $this->prenom;
        }
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFilmVo(): ?bool
    {
        return $this->film_vo;
    }

    public function setFilmVo(bool $film_vo): self
    {
        $this->film_vo = $film_vo;

        return $this;
    }

    public function getLivreVo(): ?bool
    {
        return $this->livre_vo;
    }

    public function setLivreVo(bool $livre_vo): self
    {
        $this->livre_vo = $livre_vo;

        return $this;
    }

    public function getEpisodeVo(): ?bool
    {
        return $this->episode_vo;
    }

    public function setEpisodeVo(bool $episode_vo): self
    {
        $this->episode_vo = $episode_vo;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

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
            $episodePersonne->setPersonne($this);
        }

        return $this;
    }

    public function removeEpisodePersonne(EpisodePersonne $episodePersonne): self
    {
        if ($this->episodePersonnes->contains($episodePersonne)) {
            $this->episodePersonnes->removeElement($episodePersonne);
            // set the owning side to null (unless already changed)
            if ($episodePersonne->getPersonne() === $this) {
                $episodePersonne->setPersonne(null);
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
            $livrePersonne->setPersonne($this);
        }

        return $this;
    }

    public function removeLivrePersonne(LivrePersonne $livrePersonne): self
    {
        if ($this->livrePersonnes->contains($livrePersonne)) {
            $this->livrePersonnes->removeElement($livrePersonne);
            // set the owning side to null (unless already changed)
            if ($livrePersonne->getPersonne() === $this) {
                $livrePersonne->setPersonne(null);
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
            $filmPersonne->setPersonne($this);
        }

        return $this;
    }

    public function removeFilmPersonne(FilmPersonne $filmPersonne): self
    {
        if ($this->filmPersonnes->contains($filmPersonne)) {
            $this->filmPersonnes->removeElement($filmPersonne);
            // set the owning side to null (unless already changed)
            if ($filmPersonne->getPersonne() === $this) {
                $filmPersonne->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FamillePersonne[]
     */
    public function getFamillePersonnes(): Collection
    {
        return $this->famillePersonnes;
    }

    public function addFamillePersonne(FamillePersonne $famillePersonne): self
    {
        if (!$this->famillePersonnes->contains($famillePersonne)) {
            $this->famillePersonnes[] = $famillePersonne;
            $famillePersonne->setPersonne($this);
        }

        return $this;
    }

    public function removeFamillePersonne(FamillePersonne $famillePersonne): self
    {
        if ($this->famillePersonnes->contains($famillePersonne)) {
            $this->famillePersonnes->removeElement($famillePersonne);
            // set the owning side to null (unless already changed)
            if ($famillePersonne->getPersonne() === $this) {
                $famillePersonne->setPersonne(null);
            }
        }

        return $this;
    }
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function eraseCredentials()
    {
        return true;
    }

    public function isEnabled()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function getLivreFavori(): ?Livre
    {
        return $this->livre_favori;
    }

    public function setLivreFavori(?Livre $livre_favori): self
    {
        $this->livre_favori = $livre_favori;

        return $this;
    }

    public function getFilmFavori(): ?Film
    {
        return $this->film_favori;
    }

    public function setFilmFavori(?Film $film_favori): self
    {
        $this->film_favori = $film_favori;

        return $this;
    }

    public function getSerieFavorie(): ?Serie
    {
        return $this->serie_favorie;
    }

    public function setSerieFavorie(?Serie $serie_favorie): self
    {
        $this->serie_favorie = $serie_favorie;

        return $this;
    }

    public function getEpisodeFavori(): ?Episode
    {
        return $this->episode_favori;
    }

    public function setEpisodeFavori(?Episode $episode_favori): self
    {
        $this->episode_favori = $episode_favori;

        return $this;
    }

    public function getActeurFavori(): ?Acteur
    {
        return $this->acteur_favori;
    }

    public function setActeurFavori(?Acteur $acteur_favori): self
    {
        $this->acteur_favori = $acteur_favori;

        return $this;
    }

}
