<?php

namespace App\Repository;

use App\Entity\Famille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Famille|null find($id, $lockMode = null, $lockVersion = null)
 * @method Famille|null findOneBy(array $criteria, array $orderBy = null)
 * @method Famille[]    findAll()
 * @method Famille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Famille::class);
    }

    /**
     * Retourne une liste de familles paginé en fonction de l'ordre et de la recherche courante
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
}
