<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FilmPersonneRepository")
 */
class FilmPersonne
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne", inversedBy="filmPersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $personne;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Film", inversedBy="filmPersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $film;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="filmPersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Format", inversedBy="filmPersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $format;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_achat;

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

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): self
    {
        $this->personne = $personne;

        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): self
    {
        $this->film = $film;

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
