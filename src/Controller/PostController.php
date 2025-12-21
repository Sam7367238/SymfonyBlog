<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route("/post/{id}", name: "post_show")]
    public function show(Post $post): Response {
        return new Response("");
    }
}
