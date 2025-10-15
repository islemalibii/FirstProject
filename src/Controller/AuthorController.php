<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AuthorType;

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
        $authors=$repo->listAuthorByEmail();
        return $this->render('author/listAuthors.html.twig',['list'=>$authors]);
    }

    #[Route('/addAuthor', name: 'addAuthor')]
    public function addAuthor(ManagerRegistry $doctrine): Response
    {
        $author = new Author();
        $author->setUsername("Victor Hugo");
        $author->setEmail("victor.hugo@gmail.com");
        $author->setAge(45);
        $em = $doctrine->getManager(); 

        $em->persist($author);
        $em->flush();

        return new Response("Author added successfully ");
    }

    #[Route('/addAuthorForm', name: 'addAuthorForm')]
    public function addAuthorForm(ManagerRegistry $doctrine, Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('ShowAllAuthor');
        }

        return $this->render('author/addAuthor.html.twig', [
            'formA' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}',name:'delete')]
    public function Delete( ManagerRegistry $doctrine, $id, AuthorRepository $repo){
           $author=$repo->find($id);
           if (!$author) {
                throw $this->createNotFoundException("Author not found.");
            }
           $em=$doctrine->getManager();
           $em->remove($author);
           $em->flush();
           return $this->redirectToRoute('ShowAllAuthor');
        }


    #[Route('/edit/{id}', name: 'editAuthor')]
    public function editAuthor(ManagerRegistry $doctrine, Request $request, AuthorRepository $repo, $id)
    {
        $author = $repo->find($id);
        if (!$author) {
            throw $this->createNotFoundException("Author not found!");
        }

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('ShowAllAuthor');
        }

        return $this->render('author/editAuthor.html.twig', [
            'formA' => $form->createView()
        ]);
    }


}
