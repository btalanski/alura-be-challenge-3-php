<?php

namespace App\Repository;

use App\Entity\TransactionsReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransactionsReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionsReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionsReport[]    findAll()
 * @method TransactionsReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionsReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionsReport::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TransactionsReport $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(TransactionsReport $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return boolean
     */
    public function checkIfReportDateExists(\DateTime $transactionDateTime){
        $totalRows = $this->createQueryBuilder('p')
            ->select("count(p.id)")
            ->where('p.report_date = :date')
            ->setParameter('date', $transactionDateTime->format("Y-m-d"))
            ->getQuery()
            ->getSingleScalarResult();

        return $totalRows > 0;
    }

    // /**
    //  * @return TransactionsReport[] Returns an array of TransactionsReport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TransactionsReport
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
