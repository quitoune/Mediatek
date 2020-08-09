<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActeurSaisonRepository")
 */
class ActeurSaison
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Acteur", inversedBy="acteurSaisons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $acteur;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saison", inversedBy="acteurSaisons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $saison;
    
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
    
    public function getActeur(): ?Acteur
    {
        return $this->acteur;
    }
    
    public function setActeur(?Acteur $acteur): self
    {
        $this->acteur = $acteur;
        
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
}
