<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Film|null find($id, $lockMode = null, $lockVersion = null)
 * @method Film|null findOneBy(array $criteria, array $orderBy = null)
 * @method Film[]    findAll()
 * @method Film[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    /**
     * Retourne une liste de films paginé en fonction de l'ordre et de la recherche courante
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
     * Récupération des films achetés par des personnes sur une période
     *
     * @param array $personnes
     * @param string $periode
     * @return array
     */
    public function getRecentFilm(array $personnes, string $periode = '-3 months'){
        $date = date("Y-m-d", strtotime($periode));
        $querySql = 'SELECT F.`id`, F.`slug`, F.`titre`, F.`photo_id`, FP.`date_achat`, P.`username` FROM `film` F
                    INNER JOIN `film_personne` FP ON FP.`film_id` = F.`id`
                    INNER JOIN `personne` P ON FP.`personne_id` = P.`id`
                    WHERE FP.`personne_id` IN (' . implode(', ', $personnes) . ')
                    AND FP.`date_achat` > "' . $date . '"
                    AND FP.`date_achat` IS NOT NULL
                    ORDER BY FP.`date_achat` DESC';
        
        $stmt = $this->getEntityManager()->getConnection()->prepare($querySql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
