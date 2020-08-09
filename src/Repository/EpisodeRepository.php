<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    /**
     * Retourne une liste d'épisodes paginé en fonction de l'ordre et de la recherche courante
     *
     * @param int $page
     * @param int $max
     * @param array $params
     *            [orderBy] => ordre de tri
     *            [page] => page (pagination)
     *            [repository] => repository (objet courant)
     *            [field] => champ de tri,
     *            [condition] => tableau contenant des conditions supplémentaires en dehors des filtres de l'utilisateur
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     * @return \Doctrine\ORM\Tools\Pagination\Paginator[]|mixed[]|\Doctrine\DBAL\Driver\Statement[]|array[]|NULL[]
     */
    public function findAllElements(int $page = 1, int $max = 10, $params = array())
    {
        if (! is_numeric($page)) {
            throw new \InvalidArgumentException('$page doit être un integer (' . gettype($page) . ' : ' . $page . ')');
        }
        if (! is_numeric($max)) {
            throw new \InvalidArgumentException('$max doit être un integer (' . gettype($max) . ' : ' . $max . ')');
        }
        if (! isset($params['orderBy'])) {
            throw new \InvalidArgumentException('orderBy ne sont pas présents comme clés dans $params');
        }
        $firstResult = ($page - 1) * $max;
        // pagination
        $query = $this->createQueryBuilder($params['repository'])->setFirstResult($firstResult);
        // Génération des paramètres SQL
        $query = $this->generateParamsSql($query, $params);
        
        foreach($params['orderBy'] as $field => $order){
            $query->addOrderBy($field, $order);
        }
        $query->setMaxResults($max);
        $paginator = new Paginator($query);
        // Nombre total d'éléments
        $query = $this->createQueryBuilder($params['repository'])->select('COUNT(' . $params['repository'] . '.id)');
        // Génération des paramètres SQL
        $query = $this->generateParamsSql($query, $params);
        $result = $query->getQuery()->getSingleScalarResult();
        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page non trouvée');
        }
        return array(
            'paginator' => $paginator,
            'total' => $result
        );
    }
    /**
     * Génération de la requête
     *
     * @param QueryBuilder $query
     * @param array $params
     *            [order] => ordre de tri
     *            [page] => page (pagination)
     *            [search] => tableau contenant les éléments de la recherche
     *            [repository] => repository (objet courant)
     *            [field] => champ de tri,
     *            [condition] => tableau contenant des conditions supplémentaires en dehors des filtres de l'utilisateur
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function generateParamsSql(QueryBuilder $query, array $params)
    {
        if (isset($params['jointure'])) {
            foreach ($params['jointure'] as $old => $new) {
                $query->join($old . '.' . $new, $new);
            }
        }
        if (isset($params['condition'])) {
            foreach ($params['condition'] as $condition) {
                $query->andWhere($condition);
            }
        }
        return $query;
    }
    
    /**
     * Compte le nombre de saisons que possède la personne
     *
     * @param int $personne_id
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getNombreSaisons(int $personne_id)
    {
        return $this->createQueryBuilder('e')
        ->select('COUNT(sa.id)')
        ->innerJoin('e.saison', 'sa')
        ->innerJoin('e.episodePersonnes', 'ep')
        ->andWhere('ep.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->getQuery()
        ->getSingleScalarResult();
    }
    
    /**
     * Compte le nombre de séries que possède la personne
     *
     * @param int $personne_id
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getNombreSeries(int $personne_id)
    {
        return $this->createQueryBuilder('e')
        ->select('COUNT(se.id)')
        ->innerJoin('e.saison', 'sa')
        ->innerJoin('sa.serie', 'se')
        ->innerJoin('e.episodePersonnes', 'ep')
        ->andWhere('ep.personne = :personne')
        ->setParameter('personne', $personne_id)
        ->getQuery()
        ->getSingleScalarResult();
    }
    
    /**
     * Récupération des épisodes achetés par des personnes sur une période
     *
     * @param array $personnes
     * @param string $periode
     * @return array
     */
    public function getRecentEpisode(array $personnes, string $periode = '-3 months'){
        $date = date("Y-m-d", strtotime($periode));
        $querySql = 'SELECT E.`id`, E.`slug`, E.`titre`, S.`photo_id`, EP.`date_achat`, P.`username`
                    FROM `episode` E
                    INNER JOIN `saison` S ON S.`id` = E.`saison_id`
                    INNER JOIN `episode_personne` EP ON EP.`episode_id` = E.`id`
                    INNER JOIN `personne` P ON EP.`personne_id` = P.`id`
                    WHERE EP.`personne_id` IN (' . implode(', ', $personnes) . ')
                    AND EP.`date_achat` > "' . $date . '"
                    AND EP.`date_achat` IS NOT NULL
                    ORDER BY EP.`date_achat` DESC';
        
        $stmt = $this->getEntityManager()->getConnection()->prepare($querySql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
