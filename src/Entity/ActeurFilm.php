<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActeurFilmRepository")
 */
class ActeurFilm
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Film", inversedBy="acteurFilms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $film;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Acteur", inversedBy="acteurFilms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $acteur;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $principal;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $role;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrincipal(): ?int
    {
        return $this->principal;
    }

    public function setPrincipal(int $principal): self
    {
        $this->principal = $principal;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

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

    public function getActeur(): ?Acteur
    {
        return $this->acteur;
    }

    public function setActeur(?Acteur $acteur): self
    {
        $this->acteur = $acteur;

        return $this;
    }
}
