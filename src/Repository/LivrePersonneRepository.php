<?php

namespace App\Repository;

use App\Entity\LivrePersonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LivrePersonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivrePersonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivrePersonne[]    findAll()
 * @method LivrePersonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivrePersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivrePersonne::class);
    }

    /**
     * Récupération des derniers livres achetés par un membre
     *
     * @param int $personne_id
     * @return \Doctrine\ORM\Query
     */
    public function getDerniersLivres(int $personne_id)
    {
        return $this->createQueryBuilder('lp')
        ->andWhere('lp.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->orderBy('lp.date_achat', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
    }
    
    /**
     * Compte le nombre de livres que possède la personne
     *
     * @param int $personne_id
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getNombreLivres(int $personne_id)
    {
        return $this->createQueryBuilder('lp')
        ->select('COUNT(lp.id)')
        ->andWhere('lp.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->getQuery()
        ->getSingleScalarResult();
    }
    
    /**
     * Renvoie, pour un livre donné, la liste des propriétaires parmi les personnes données
     * 
     * @param int $livre
     * @param array $personnes
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getLivrePersonnes(int $livre, array $personnes)
    {
        return $this->createQueryBuilder('lp')
        ->andWhere('lp.livre = ' . $livre)
        ->andWhere('lp.personne IN (' . implode(",", $personnes) . ')')
        ->getQuery()
        ->getResult();
    }
}
