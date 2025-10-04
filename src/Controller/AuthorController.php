<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{

    //tp3
    private array $authors = [
        [
            'id' => 1,
            'picture' => 'assets/images/Victor_Hugo.jpg',
            'username' => 'Victor Hugo',
            'email' => 'victor.hugo@gmail.com',
            'nb_books' => 100,
        ],
        [
            'id' => 2,
            'picture' => 'assets/images/Shakespeare.jpg',
            'username' => 'William Shakespeare',
            'email' => 'william.shakespeare@gmail.com',
            'nb_books' => 200,
        ],
        [
            'id' => 3,
            'picture' => 'assets/images/TahaHussein.jpg',
            'username' => 'Taha Hussein',
            'email' => 'taha.hussein@gmail.com',
            'nb_books' => 300,
        ],
    ];

    #[Route('/showAuthor/{name}', name: 'app_author')]
    public function showAuthor($name)
    {
        return $this->render('author/show.html.twig', [
            'name' => $name,         ]);
    }

    #[Route('/afficher', name: 'afficher')]
    public function Afficher(): Response{
        return new Response('hello');
    }


    #[Route('/listAuthors', name: 'app_list')]
    public function listAuthors(): Response
    {
        return $this->render('author/list.html.twig', [
            'authors' => $this->authors,
        ]);
    }


    #[Route('/author/{id}', name: 'author_details')]
    public function authorDetails(int $id): Response
    {
        $author = $this->authors[$id - 1] ?? null;

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }

    //tp4

    #[Route('/ShowAllAuthor', name: 'ShowAllAuthor')]
    public function ShowAllAuthor(AuthorRepository $repo){
        $authors=$repo->findAll();
        return $this->render('author/listAuthors.html.twig',['list'=>$authors]);
    }

    #[Route('/addAuthor', name: 'addAuthorStatic')]
    public function addAuthorStatic(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager(); 
        $author = new Author();
        $author->setUsername("Victor Hugo");
        $author->setEmail("victor.hugo@gmail.com");
        $author->setAge(45);

        $em->persist($author);
        $em->flush();

        return new Response("Author added successfully : " . $author->getUsername());
    }
}
