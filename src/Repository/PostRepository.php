<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getRecentPostsArray($number = 9, $exclude_categories = array()){
        $qb = $this->createQueryBuilder('p')
                ->innerJoin('p.categories', 'c')
                ->where('p.published = :val')
                ->setParameter('val', 1);
        
        if(count($exclude_categories) > 0){
            $qb->andWhere('c.id NOT IN (:exclude)')->setParameter('exclude', $exclude_categories);
        }

        return $qb->orderBy('p.createDateTime', 'DESC')
                ->setMaxResults($number)
                ->getQuery()
                ->getResult();
    }

    public function getPostsPaginated($page = 1, $limit = 9, $exclude_categories = array()){
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->where('p.published = :val')
            ->setParameter('val', 1);

        if(count($exclude_categories) > 0){
            $qb->andWhere('c.id NOT IN (:exclude)')->setParameter('exclude', $exclude_categories);
        }
            
            
        return $qb->orderBy('p.createDateTime', 'DESC')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getPostCount($exclude_categories = array())
    {
        $qb =  $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->select('count(p)')
            ->where('p.published = :val')
            ->setParameter('val', 1);

        if(count($exclude_categories) > 0){
            $qb->andWhere('c.id NOT IN (:exclude)')->setParameter('exclude', $exclude_categories);
        }

        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    public function getPostsPaginatedByCategory($page = 1, $limit = 9, $categoryId){
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->where('c.id = :category_id')
            ->setParameter('category_id', $categoryId)
            ->andWhere('p.published = :val')
            ->setParameter('val', 1)
            ->orderBy('p.createDateTime', 'DESC')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getPostCountByCategory($categoryId)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p)')
            ->innerJoin('p.categories', 'c')
            ->where('c.id = :category_id')
            ->setParameter('category_id', $categoryId)
            ->andWhere('p.published = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
