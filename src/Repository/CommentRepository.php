<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllCommentAdmin()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id','c.comment_text', 'c.createDate', 'u.name AS user_name', 'ar.title AS article_title')
            ->leftJoin('c.user', 'u')
            ->leftJoin('c.article', 'ar')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    // $id paramètres d'entrée &&  : ?array paramètre de sortie
    // public function findAllMusicStyleArtistById($id): ?array
    // {
    //     $queryBuilder = $this->createQueryBuilder('m')
    //         ->select('m.id', 'm.name', 'm.img', 's.name AS style_name', 'ar.name AS artist_name')
    //         ->leftJoin('m.artist', 'ar')
    //         ->leftJoin('m.style', 's')
    //         ->andWhere('m.id = :id')
    //         ->setParameter('id', $id)
    //         ->getQuery();
        //Là ça fonctionne pas parce que ça retourne des arrays
        // return $queryBuilder->getQuery()->getResult();
        //Avec ça, ça va, mais il faut rappeler le getQuery au dessus. Et ça ne renvoie pas dans un array vide et renvoie une seule entité, donc plus besoin du [0]
    //     return $queryBuilder->getOneOrNullResult();
    // }

//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
