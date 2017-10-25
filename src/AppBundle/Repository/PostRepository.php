<?php

namespace AppBundle\Repository;

/**
 * Class PostRepository
 * @package AppBundle\Repository
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function getByUser($user_id)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.createdBy', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $user_id);

        return $qb->getQuery()->getResult();

    }
}
