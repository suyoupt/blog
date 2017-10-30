<?php

namespace AppBundle\Repository;

/**
 * Class PostRepository
 * @package AppBundle\Repository
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function getQbByUserAndKeyword($userId, $word)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.createdBy', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $userId);


        if (!empty($word)) {
            $qb
                ->andWhere('p.title LIKE :word OR p.body LIKE :word')
                ->setParameter('word', "%$word%");
        }


        return $qb;
    }

    public function getByUserAndKeyword($user_id, $word)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.createdBy', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $user_id);


        if (!empty($word)) {
            $qb
                ->andWhere('p.title LIKE :word OR p.body LIKE :word')
                ->setParameter('word', "%$word%");
        }


        return $qb->getQuery()->getResult();

    }
}
