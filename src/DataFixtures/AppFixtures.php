<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('test@email.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'TestPassword'));
        $manager->persist($user);

        $post = new Post();
        $post->setTitle('[1] Post Title');
        $post->setContent('Post content.');
        $post->setUser($user);
        $manager->persist($post);

        $post = new Post();
        $post->setTitle('[2] Post Title');
        $post->setContent('Post content.');
        $post->setUser($user);
        $manager->persist($post);

        $comment = new Comment();
        $comment->setContent('Comment');
        $comment->setPost($post);
        $comment->setUser($user);
        $manager->persist($comment);

        $manager->flush();
    }
}
