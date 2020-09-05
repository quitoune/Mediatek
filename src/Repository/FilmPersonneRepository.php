<?php

namespace App\Repository;

use App\Entity\FilmPersonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FilmPersonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilmPersonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilmPersonne[]    findAll()
 * @method FilmPersonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmPersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilmPersonne::class);
    }

    /**
     * Récupération des derniers films achetés par un membre
     *
     * @param int $personne_id
     * @return \Doctrine\ORM\Query
     */
    public function getDerniersFilms(int $personne_id)
    {
        return $this->createQueryBuilder('fp')
        ->andWhere('fp.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->orderBy('fp.date_achat', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
    }
    
    /**
     * Compte le nombre de films que possède la personne
     *
     * @param int $personne_id
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getNombreFilms(int $personne_id)
    {
        return $this->createQueryBuilder('fp')
        ->select('COUNT(fp.id)')
        ->andWhere('fp.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->getQuery()
        ->getSingleScalarResult();
    }
    
    /**
     * Renvoie, pour un film, donné la liste des propriétaires parmi les personnes données
     * 
     * @param int $film
     * @param array $personnes
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getFilmPersonnes(int $film, array $personnes)
    {
        return $this->createQueryBuilder('fp')
        ->andWhere('fp.film = ' . $film)
        ->andWhere('fp.personne IN (' . implode(",", $personnes) . ')')
        ->getQuery()
        ->getResult();
    }
}
