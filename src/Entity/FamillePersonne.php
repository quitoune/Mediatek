<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamillePersonneRepository")
 */
class FamillePersonne
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Famille", inversedBy="famillePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $famille;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne", inversedBy="famillePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $personne;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getFamille(): ?Famille
    {
        return $this->famille;
    }
    
    public function setFamille(?Famille $famille): self
    {
        $this->famille = $famille;
        
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
    
    public function getAdmin(): ?bool
    {
        return $this->admin;
    }
    
    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;
        
        return $this;
    }
}
