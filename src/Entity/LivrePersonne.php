<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivrePersonneRepository")
 */
class LivrePersonne
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne", inversedBy="livrePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $personne;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Livre", inversedBy="livrePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livre;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="livrePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Format", inversedBy="livrePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $format;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_achat;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $isbn;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->date_achat;
    }

    public function setDateAchat(?\DateTimeInterface $date_achat): self
    {
        $this->date_achat = $date_achat;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): self
    {
        $this->personne = $personne;

        return $this;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): self
    {
        $this->livre = $livre;

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

    public function getFormat(): ?Format
    {
        return $this->format;
    }

    public function setFormat(?Format $format): self
    {
        $this->format = $format;

        return $this;
    }
}
