<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EpisodePersonneRepository")
 */
class EpisodePersonne
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_achat;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne", inversedBy="episodePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $personne;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Episode", inversedBy="episodePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $episode;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="episodePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Format", inversedBy="episodePersonnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $format;

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

    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }

    public function setEpisode(?Episode $episode): self
    {
        $this->episode = $episode;

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
