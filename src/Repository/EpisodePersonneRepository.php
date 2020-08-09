<?php

namespace App\Repository;

use App\Entity\EpisodePersonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EpisodePersonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpisodePersonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpisodePersonne[]    findAll()
 * @method EpisodePersonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodePersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpisodePersonne::class);
    }

    /**
     * Récupération des derniers épisodes achetés par un membre
     *
     * @param int $personne_id
     * @return \Doctrine\ORM\Query
     */
    public function getDerniersEpisodes(int $personne_id)
    {
        return $this->createQueryBuilder('ep')
        ->andWhere('ep.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->orderBy('ep.date_achat', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
    }
    
    /**
     * Compte le nombre d'épisode que possède la personne
     *
     * @param int $personne_id
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getNombreEpisodes(int $personne_id)
    {
        return $this->createQueryBuilder('ep')
        ->select('COUNT(ep.id)')
        ->andWhere('ep.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->getQuery()
        ->getSingleScalarResult();
    }
}
