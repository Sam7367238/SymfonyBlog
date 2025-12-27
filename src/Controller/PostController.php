<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/post', 'post_')]
final class PostController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->postRepository->createQueryBuilder('p');

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('post/index.html.twig', compact('posts'));
    }

    #[Route("/{id<\d+>}", name: 'show')]
    public function show(Post $post, Request $request, #[CurrentUser] User $user): Response
    {
        $comments = $post->getComments();
        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setPost($post);
            $comment->setUser($user);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash('status', 'Comment Added Successfully');

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', compact('post', 'commentForm', 'comments'));
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, #[CurrentUser] User $user): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user);

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('status', 'Post Created Successfully');

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/new.html.twig', compact('form'));
    }

    #[Route("/{id<\d+>}/edit", name: 'edit')]
    #[IsGranted('edit', 'post')]
    public function edit(Post $post, Request $request): Response
    {
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('status', 'Post Edited Successfully');

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', compact('form'));
    }

    #[Route("/{id<\d+>}/delete", name: 'delete')]
    #[IsGranted('delete', 'post')]
    public function delete(Request $request, Post $post): Response
    {
        if ($request->isMethod('POST')) {
            $this->entityManager->remove($post);
            $this->entityManager->flush();

            $this->addFlash('status', 'Post Deleted Successfully');

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/delete.html.twig', ['id' => $post->getId()]);
    }
}
