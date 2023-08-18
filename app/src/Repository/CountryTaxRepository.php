<?php

namespace App\Repository;

use App\Entity\CountryTax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CountryTax>
 *
 * @method CountryTax|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryTax|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountryTax[]    findAll()
 * @method CountryTax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryTaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryTax::class);
    }

    public function getTaxesByCountryCode($code): iterable
    {
        return $this->createQueryBuilder('ct')
            ->select('ct.id, c.id AS country, c.code, ct.value, ct.rule')
            ->innerJoin('App\Entity\Country', 'c', Join::WITH, 'ct.country = c.id')
            ->where('c.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult();
    }
}
