<?php

namespace App\Repository;

use App\Entity\FamillePersonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FamillePersonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamillePersonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamillePersonne[]    findAll()
 * @method FamillePersonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamillePersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FamillePersonne::class);
    }

    public function getFamillesInfos(int $id){
        
        $queryFam = 'SELECT  F.id, F.nom
                    FROM famille as F
                    INNER JOIN famille_personne as FP ON F.id = FP.famille_id
                    WHERE personne_id = ' . $id;
        $stmt = $this->getEntityManager()->getConnection()->prepare($queryFam);
        $stmt->execute();
        $resultFam = $stmt->fetchAll();
        
        $familles = array();
        foreach ($resultFam as $fam){
            $familles[] = $fam['id'];
        }
        
        $queryPers = 'SELECT P.id, P.nom, P.prenom, P.username
                    FROM personne as P
                    INNER JOIN famille_personne as FP ON P.id = FP.personne_id
                    WHERE FP.famille_id IN (' . implode(', ', $familles) . ')';
        
        $stmt = $this->getEntityManager()->getConnection()->prepare($queryPers);
        $stmt->execute();
        $resultPers = $stmt->fetchAll();
        
        $personnes = array();
        foreach ($resultPers as $pers){
            $personnes[$pers['id']] = $pers;
        }
        
        $queryLieux = 'SELECT L.id, L.nom
                    FROM lieu as L
                    INNER JOIN famille_lieu as FL ON L.id = FL.lieu_id
                    WHERE FL.famille_id IN (' . implode(', ', $familles) . ')';
        
        $stmt = $this->getEntityManager()->getConnection()->prepare($queryLieux);
        $stmt->execute();
        $resultLieux = $stmt->fetchAll();
        
        $lieux = array();
        foreach ($resultLieux as $lieu){
            $lieux[$lieu['id']] = $lieu;
        }
        
        $familles = array();
        foreach ($resultFam as $fam){
            $familles[$fam['id']] = $fam;
        }
        
        return array('familles' => $familles, 'personnes' => $personnes, 'lieux' => $lieux);
    }
}
