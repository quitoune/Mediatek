<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Personne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personne[]    findAll()
 * @method Personne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    /**
     * Retourne une liste de personnes paginé en fonction de l'ordre et de la recherche courante
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
     * Récupération des personnes appartenant aux familles de la personne donnée
     *
     * @param int $personne_id
     * @return array
     */
    public function getMembreAssocie(int $personne_id){
        
        $querySql = 'SELECT P.`id`, P.`prenom`, P.`nom`
                        FROM `personne` P
                        INNER JOIN `famille_personne` PF ON PF.`personne_id` = P.`id`
                        INNER JOIN `famille` F ON F.`id` = PF.`famille_id`
                        INNER JOIN `famille_personne` FP ON FP.`famille_id` = F.`id`
                        INNER jOIN `personne` P2 ON FP.`personne_id` = P2.`id`
                        WHERE P2.`id` = ' . $personne_id;
        
        $stmt = $this->getEntityManager()->getConnection()->prepare($querySql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * 
     * @param array $personnes
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getPersonnesAvecId(array $personnes){
        return $this->createQueryBuilder('p')
        ->andWhere('p.id IN (' . implode(",", $personnes) . ')')
        ->addOrderBy('p.nom', 'ASC')
        ->addOrderBy('p.prenom', 'ASC')
        ->addOrderBy('p.username', 'ASC')
        ->getQuery()
        ->getResult();
    }
}
