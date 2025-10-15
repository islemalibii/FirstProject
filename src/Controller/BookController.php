<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBookForm', name: 'addBookForm')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setEnabled(true);

            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);

            $em->persist($book);
            $em->persist($author);
            $em->flush();

            $this->addFlash('success', 'Book added successfully!');
            return $this->redirectToRoute('ShowPublishedBooks');
        }

        return $this->render('book/addBook.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/ShowBooks', name: 'ShowPublishedBooks')]
    public function ShowPublishedBooks(Request $request, BookRepository $repo)
    {
        $authorName = $request->query->get('author');
        if ($authorName) {
            $books = $repo->searchBookByRef($authorName);
        } else {
            $books = $repo->findBy(['enabled' => true]);
        }
    
        $unpublishedBooks = $repo->findBy(['enabled' => false]);

        return $this->render('book/showBook.html.twig', [
        'books' => $books,
        'countPublished' => count($repo->findBy(['enabled' => true])),
        'countUnpublished' => count($unpublishedBooks),
        'authorName' => $authorName, 
    ]);
    }

    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function edit(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Book updated successfully!');
            return $this->redirectToRoute('ShowPublishedBooks');
        }

        return $this->render('book/editBook.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }
    #[Route('/book/delete/{id}', name: 'book_delete')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $author = $book->getAuthor();
        $em->remove($book);

        $author->setNbBooks(max(0, $author->getNbBooks() - 1));
        $em->flush();

        if ($author->getNbBooks() === 0) {
            $em->remove($author);
        }
        $em->flush();


        return $this->redirectToRoute('ShowPublishedBooks');
    }
    #[Route('/book/show/{id}', name: 'book_show')]
    public function show(Book $book): Response
    {
        return $this->render('book/showBookDetails.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/books/byAuthor', name: 'books_by_author')]
    public function booksByAuthor(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->booksListByAuthors();

        return $this->render('book/booksByAuthor.html.twig', [
            'books' => $books,
        ]);
    }


    #[Route('/books/filter', name: 'books_filter')]
    public function filter(BookRepository $repo): Response
    {
        $books = $repo->findBooksBefore2023();

        return $this->render('book/filterBooks.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/updateCategory', name: 'books_update_category')]
    public function updateCategory(BookRepository $repo): Response
    {
        $count = $repo->updateScienceFictionToRomance();

        $this->addFlash('success', "$count books updated from Science-Fiction to Romance");

        return $this->redirectToRoute('ShowPublishedBooks');
    }







}
