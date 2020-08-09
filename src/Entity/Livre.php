<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivreRepository")
 */
class Livre
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
    private $auteur;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $premiere_edition;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Type", inversedBy="livres")
     */
    private $type;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Categorie", inversedBy="livres")
     */
    private $categories;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saga", inversedBy="livres")
     */
    private $saga;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="livres")
     */
    private $photo;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tome;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LivrePersonne", mappedBy="livre", cascade={"persist"})
     */
    private $livrePersonnes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personne", mappedBy="livre_favori")
     */
    private $personnes;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->livrePersonnes = new ArrayCollection();
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

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getPremiereEdition(): ?int
    {
        return $this->premiere_edition;
    }

    public function setPremiereEdition(?int $premiere_edition): self
    {
        $this->premiere_edition = $premiere_edition;

        return $this;
    }

    public function getTome(): ?int
    {
        return $this->tome;
    }

    public function setTome(?int $tome): self
    {
        $this->tome = $tome;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

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
            $livrePersonne->setLivre($this);
        }

        return $this;
    }

    public function removeLivrePersonne(LivrePersonne $livrePersonne): self
    {
        if ($this->livrePersonnes->contains($livrePersonne)) {
            $this->livrePersonnes->removeElement($livrePersonne);
            // set the owning side to null (unless already changed)
            if ($livrePersonne->getLivre() === $this) {
                $livrePersonne->setLivre(null);
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
            $personne->setLivreFavori($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->contains($personne)) {
            $this->personnes->removeElement($personne);
            // set the owning side to null (unless already changed)
            if ($personne->getLivreFavori() === $this) {
                $personne->setLivreFavori(null);
            }
        }

        return $this;
    }
}
