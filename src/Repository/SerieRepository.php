<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function save(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBestSeries()
    {
        // en DQL
/*        $entityManager = $this->getEntityManager();

        $dql = "SELECT s FROM App\Entity\Serie as s
                WHERE s.vote >= 8.4
                AND s.popularity > 200
                ORDER BY s.popularity DESC";

        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(50);

        return $query->getResult();*/

        // avec le Query Builder
        $qb = $this->createQueryBuilder('s');

        $qb->andWhere('s.vote >= 0')
            ->andWhere('s.popularity > 0')
            ->addOrderBy('s.popularity', 'DESC');

        $query = $qb->getQuery();
        $query->setMaxResults(50);

        return $query->getResult();

    }


    public function findSeriesWithPagination(int $page){

        $qb = $this->createQueryBuilder('s');
        $qb->addOrderBy('s.popularity', 'DESC');
        $qb->leftJoin('s.seasons', 'season');
        $qb->addSelect('season');


        $query = $qb->getQuery();

        // limit
        $query->setMaxResults(Serie::MAX_RESULT);



        // offset
        $offset = ($page - 1) * Serie::MAX_RESULT;
        $query->setFirstResult($offset);

        $paginator = new Paginator($query);

        return $paginator;
    }

//    /**
//     * @return Serie[] Returns an array of Serie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Serie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
