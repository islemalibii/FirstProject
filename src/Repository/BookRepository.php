<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchBookByRef(string $authorName): array
    {
        $req = $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->where('a.username LIKE :username')
            ->setParameter('username', '%' . $authorName . '%')
            ->orderBy('b.title', 'ASC')
            ->getQuery()->getResult();
        return $req;
    }
    public function booksListByAuthors(): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->orderBy('a.username', 'ASC')   
            ->addOrderBy('b.title', 'ASC') 
            ->getQuery()
            ->getResult();
    }


    public function findBooksBefore2023(): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->where('b.enabled = :enabled')
            ->andWhere('b.publicationDate < :date')
            ->andWhere('a.nb_books > :minBooks')
            ->setParameter('enabled', true)
            ->setParameter('date', new \DateTime('2023-01-01'))
            ->setParameter('minBooks', 10)
            ->orderBy('b.publicationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function updateScienceFictionToRomance(): int
    {
        return $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction')
            ->getQuery()
            ->execute(); 
    }


}
