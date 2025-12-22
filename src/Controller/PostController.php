<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    
    public function __construct(private readonly PostRepository $postRepository) {}

    #[Route('/posts', name: 'post_index')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $this -> postRepository -> findAll(),
        ]);
    }

    #[Route("/post/{id<\d+>}", name: "post_show")]
    public function show(Post $post): Response {
        return $this -> render("post/show.html.twig", compact("post"));
    }

    #[Route("/post/new", name: "post_new")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response {
        $post = new Post();
        $form = $this -> createForm(PostType::class, $post);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $entityManager -> persist($post);
            $entityManager -> flush();

            $this -> addFlash("status", "Post Created Successfully");

            return $this -> redirectToRoute("post_show", ["id" => $post -> getId()]);
        }

        return $this -> render("post/new.html.twig", compact("form"));
    }

    #[Route("/post/{id<\d+>}/edit", name: "post_edit")]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response {
        $form = $this -> createForm(PostType::class, $post);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $entityManager -> flush();

            $this -> addFlash("status", "Post Edited Successfully");

            return $this -> redirectToRoute("post_show", ["id" => $post -> getId()]);
        }

        return $this -> render("post/edit.html.twig", compact("form"));
    }

    #[Route("/post/{id<\d+>}/delete", name: "post_delete")]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response {
        if ($request -> isMethod("POST")) {
            $entityManager -> remove($post);
            $entityManager -> flush();

            $this -> addFlash("status", "Post Deleted Successfully");

            return $this -> redirectToRoute("post_index");
        }

        return $this -> render("post/delete.html.twig", ["id" => $post -> getId()]);
    }
}
