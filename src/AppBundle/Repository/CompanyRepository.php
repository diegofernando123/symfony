<?php

namespace AppBundle\Repository;

/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends \Doctrine\ORM\EntityRepository
{
	 public function findCompaniesByLetter($letter)
    {
        return $this->getEntityManager()->createQuery("SELECT c FROM AppBundle:Company c WHERE c.name LIKE :letter")->setParameter('letter', $letter . '%')->getResult();
    }

    public function companyCount($letter)
    {
        return $this->createQueryBuilder('c')
            ->select('c, COUNT(votes) AS company_votes')
            ->leftJoin('c.votes', 'votes')
            ->where("c.name LIKE :letter")
            ->andWhere('c.blocked != :status')
            ->groupBy('c.id')
            ->orderBy('company_votes', 'DESC')
            ->setParameters(['letter' =>  $letter . '%',  'status' => true])
            ->getQuery()
            ->getResult();
    }

    public function userCompanies($user)
    {
        return $this->createQueryBuilder('c')
            ->select('c, COUNT(votes) AS company_votes')
            ->leftJoin('c.votes', 'votes')
            ->where("c.user = :user")
            ->andWhere('c.blocked != :status')
            ->groupBy('c.id')
            ->orderBy('company_votes', 'DESC')
            ->setParameters(['user' =>  $user . '%',  'status' => true])
            ->getQuery()
            ->getResult();

    }
}
